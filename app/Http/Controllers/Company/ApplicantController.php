<?php

namespace App\Http\Controllers\Company;

use App\Enums\ApplicationStatus;
use App\Enums\InterviewStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Company\DecideApplicationRequest;
use App\Http\Requests\Company\NoteApplicationRequest;
use App\Models\Application;
use App\Models\JobPosting;
use App\Services\ApplicationStatusService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ApplicantController extends Controller
{
    public function __construct(private readonly ApplicationStatusService $statusService) {}

    /**
     * Daftar pelamar untuk satu lowongan.
     */
    public function index(Request $request, JobPosting $job): View
    {
        $this->authorize('viewApplicants', $job);

        $applications = Application::forCompany($job->companyProfile->id)
            ->where('job_posting_id', $job->id)
            ->with(['applicantProfile.user', 'applicantProfile.skills'])
            ->when($request->get('status'), fn ($q, $s) => $q->where('status', $s))
            ->when($request->get('q'), function ($q, $search) {
                $q->whereHas('applicantProfile.user', fn ($u) => $u->where('name', 'like', '%'.escapeLike($search).'%'));
            })
            ->when($request->get('sort') === 'profile', fn ($q) => $q->join(
                'applicant_profiles', 'applications.applicant_profile_id', '=', 'applicant_profiles.id'
            )->orderByDesc('applicant_profiles.alumni_verified_at'))
            ->select('applications.*')
            ->latest('applications.created_at')
            ->paginate(15)->withQueryString();

        // Funnel: jumlah per status
        $funnel = Application::where('job_posting_id', $job->id)
            ->selectRaw('status, count(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        return view('company.applicants.index', compact('job', 'applications', 'funnel'));
    }

    /**
     * Detail pelamar. Auto-viewed (BR6) saat pertama kali dibuka perusahaan pemilik.
     * Ini satu-satunya pengecualian "GET tidak mengubah data" — idempoten, hanya sekali.
     */
    public function show(Request $request, Application $application): View
    {
        $this->authorize('view', $application);

        $application->load([
            'applicantProfile.user',
            'applicantProfile.skills',
            'applicantProfile.portfolioProjects',
            'applicantProfile.currentCv',
            'jobPosting.companyProfile',
            'statusHistories' => fn ($q) => $q->orderBy('created_at'),
            'cvDocument',
            'interviews' => fn ($q) => $q->orderByDesc('scheduled_at'),
        ]);

        // Auto-viewed: jika status masih "applied" dan dibuka oleh perusahaan pemilik
        $user = $request->user();
        if ($application->status === ApplicationStatus::Applied
            && $user->companyProfile?->id === $application->jobPosting->company_profile_id
        ) {
            try {
                $this->statusService->transition($application, ApplicationStatus::Viewed, $user);
                $application->refresh();
            } catch (\DomainException) {
                // Gagal diam-diam — halaman tetap tampil
            }
        }

        return view('company.applicants.show', compact('application'));
    }

    /**
     * Keputusan: terima atau tolak.
     */
    public function decide(DecideApplicationRequest $request, Application $application): RedirectResponse
    {
        $this->authorize('decide', $application);

        $to = ApplicationStatus::from($request->validated('decision'));

        try {
            $this->statusService->transition(
                $application,
                $to,
                $request->user(),
                $request->validated('note')
            );
        } catch (\DomainException $e) {
            return back()->withErrors(['decide' => $e->getMessage()]);
        }

        // Jika ada interview aktif saat keputusan diambil, tandai selesai
        $application->interviews()
            ->whereIn('status', [
                InterviewStatus::Scheduled->value,
                InterviewStatus::Confirmed->value,
                InterviewStatus::RescheduleRequested->value,
            ])
            ->update(['status' => InterviewStatus::Completed->value]);

        $label = $to === ApplicationStatus::Accepted ? 'diterima' : 'ditolak';

        return back()->with('toast', "Pelamar berhasil {$label}.");
    }

    /**
     * Tambah catatan privat perusahaan — tidak pernah terlihat pelamar.
     */
    public function note(NoteApplicationRequest $request, Application $application): RedirectResponse
    {
        $this->authorize('decide', $application);

        $application->company_note = $request->validated('company_note');
        $application->save();

        return back()->with('toast', 'Catatan berhasil disimpan.');
    }
}
