<?php

namespace App\Http\Controllers\Applicant;

use App\Enums\ApplicationStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Applicant\StoreApplicationRequest;
use App\Models\Application;
use App\Models\JobPosting;
use App\Notifications\ApplicationReceived;
use App\Services\ApplicationStatusService;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class ApplicationController extends Controller
{
    public function __construct(private readonly ApplicationStatusService $statusService) {}

    public function index(Request $request): View
    {
        $profile = $request->user()->applicantProfile;
        $applications = Application::where('applicant_profile_id', $profile->id)
            ->with(['jobPosting.companyProfile'])
            ->when($request->get('status'), fn ($q, $s) => $q->where('status', $s))
            ->latest()
            ->paginate(10)->withQueryString();

        $counts = Application::where('applicant_profile_id', $profile->id)
            ->selectRaw('status, count(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        return view('applicant.applications.index', compact('applications', 'counts'));
    }

    public function show(Request $request, Application $application): View
    {
        $this->authorize('view', $application);

        $application->load([
            'jobPosting.companyProfile',
            'jobPosting.skills',
            'statusHistories' => fn ($q) => $q->orderBy('created_at'),
            'cvDocument',
            'interviews' => fn ($q) => $q->orderByDesc('scheduled_at'),
        ]);

        return view('applicant.applications.show', compact('application'));
    }

    /**
     * Simpan lamaran baru — throttle 20x/menit per user.
     * Cek BR1-BR3, buat Application + history awal dalam satu transaksi.
     */
    public function store(StoreApplicationRequest $request, string $slug): RedirectResponse
    {
        $user = $request->user();
        $profile = $user->applicantProfile;
        $job = JobPosting::where('slug', $slug)->firstOrFail();

        // BR1: cek sudah pernah melamar
        if (Application::where('job_posting_id', $job->id)
            ->where('applicant_profile_id', $profile->id)->exists()
        ) {
            return back()->withErrors(['apply' => 'Kamu sudah melamar lowongan ini.']);
        }

        // BR2: lowongan harus open, deadline belum lewat, perusahaan approved
        if (! JobPosting::open()->where('id', $job->id)->exists()) {
            return back()->withErrors(['apply' => 'Lowongan ini sudah tidak tersedia.']);
        }

        // BR3: pelamar harus punya CV aktif
        $cvId = $request->validated('cv_document_id') ?? $profile->current_cv_document_id;
        if (! $cvId) {
            return back()->withErrors(['apply' => 'Kamu harus memiliki CV aktif sebelum melamar.']);
        }

        try {
            $application = DB::transaction(function () use ($profile, $job, $cvId, $request, $user): Application {
                $app = new Application([
                    'job_posting_id' => $job->id,
                    'applicant_profile_id' => $profile->id,
                    'cv_document_id' => $cvId,
                    'cover_letter' => $request->validated('cover_letter'),
                ]);
                $app->status = ApplicationStatus::Applied;
                $app->save();

                // History awal: null → applied
                $app->statusHistories()->create([
                    'from_status' => null,
                    'to_status' => ApplicationStatus::Applied->value,
                    'changed_by' => $user->id,
                    'created_at' => now(),
                ]);

                return $app;
            });
        } catch (UniqueConstraintViolationException) {
            return back()->withErrors(['apply' => 'Kamu sudah melamar lowongan ini.']);
        }

        // Notifikasi ke pelamar (konfirmasi) dan perusahaan (lamaran baru)
        $user->notify(new ApplicationReceived($application));
        $job->companyProfile->user->notify(new ApplicationReceived($application));

        return redirect()->route('applicant.applications.show', $application)
            ->with('toast', 'Lamaran berhasil dikirim!');
    }

    /** Batalkan lamaran (withdraw) */
    public function withdraw(Request $request, Application $application): RedirectResponse
    {
        $this->authorize('withdraw', $application);

        try {
            $this->statusService->transition(
                $application,
                ApplicationStatus::Withdrawn,
                $request->user(),
                'Dibatalkan oleh pelamar.'
            );
        } catch (\DomainException $e) {
            return back()->withErrors(['withdraw' => $e->getMessage()]);
        }

        return back()->with('toast', 'Lamaran berhasil dibatalkan.');
    }
}
