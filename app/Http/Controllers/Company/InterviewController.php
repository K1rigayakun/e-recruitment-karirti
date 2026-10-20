<?php

namespace App\Http\Controllers\Company;

use App\Http\Controllers\Controller;
use App\Http\Requests\Company\StoreInterviewRequest;
use App\Http\Requests\Company\UpdateInterviewRequest;
use App\Models\Application;
use App\Models\Interview;
use App\Notifications\InterviewCancelled;
use App\Notifications\InterviewRescheduled;
use App\Notifications\InterviewScheduled;
use App\Services\InterviewScheduler;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\View\View;

class InterviewController extends Controller
{
    public function __construct(private readonly InterviewScheduler $scheduler) {}

    /** Halaman agenda: daftar interview per hari + kalender bulan */
    public function index(Request $request): View
    {
        $profile = $request->user()->companyProfile;
        $month = $request->integer('month', now()->month);
        $year = $request->integer('year', now()->year);

        $interviews = Interview::whereHas(
            'application.jobPosting',
            fn ($q) => $q->where('company_profile_id', $profile->id)
        )
            ->with(['application.applicantProfile.user', 'application.jobPosting'])
            ->when($request->get('filter') === 'past',
                fn ($q) => $q->where('scheduled_at', '<', now()),
                fn ($q) => $q->where('scheduled_at', '>=', now())
            )
            ->orderBy('scheduled_at')
            ->paginate(15)->withQueryString();

        // Kalender bulan: kumpulkan tanggal yang punya interview
        $calendarDates = Interview::whereHas(
            'application.jobPosting',
            fn ($q) => $q->where('company_profile_id', $profile->id)
        )
            ->whereYear('scheduled_at', $year)
            ->whereMonth('scheduled_at', $month)
            ->pluck('scheduled_at')
            ->map(fn ($d) => Carbon::parse($d)->day)
            ->unique()
            ->values();

        return view('company.interviews.index', compact('interviews', 'calendarDates', 'month', 'year'));
    }

    /** Jadwalkan interview baru untuk satu lamaran */
    public function store(StoreInterviewRequest $request, Application $application): RedirectResponse
    {
        // Hanya perusahaan pemilik lowongan
        abort_unless(
            $request->user()->companyProfile?->id === $application->jobPosting->company_profile_id,
            403
        );

        abort_if($application->status->isFinal(), 403, 'Lamaran sudah final.');

        $data = $request->validated();
        $data['scheduled_at'] = Carbon::parse($data['scheduled_at'])->setTimezone('Asia/Jakarta');

        try {
            $interview = $this->scheduler->schedule($application, $request->user(), $data);
        } catch (\DomainException $e) {
            return back()->withErrors(['schedule' => $e->getMessage()]);
        }

        // Notifikasi ke pelamar
        $application->applicantProfile->user->notify(new InterviewScheduled($interview));

        return back()->with('toast', 'Interview berhasil dijadwalkan.');
    }

    /** Update jadwal interview */
    public function update(UpdateInterviewRequest $request, Interview $interview): RedirectResponse
    {
        abort_unless(
            $request->user()->companyProfile?->id === $interview->application->jobPosting->company_profile_id,
            403
        );

        $data = $request->validated();
        $data['scheduled_at'] = Carbon::parse($data['scheduled_at'])->setTimezone('Asia/Jakarta');

        try {
            $this->scheduler->reschedule($interview, $request->user(), $data);
        } catch (\DomainException $e) {
            return back()->withErrors(['schedule' => $e->getMessage()]);
        }

        // Notifikasi ke pelamar
        $interview->application->applicantProfile->user->notify(new InterviewRescheduled($interview));

        return back()->with('toast', 'Jadwal interview berhasil diubah.');
    }

    /** Batalkan interview */
    public function destroy(Request $request, Interview $interview): RedirectResponse
    {
        abort_unless(
            $request->user()->companyProfile?->id === $interview->application->jobPosting->company_profile_id,
            403
        );

        $this->scheduler->cancel($interview, $request->user());

        // Notifikasi ke pelamar
        $interview->application->applicantProfile->user->notify(new InterviewCancelled($interview));

        return back()->with('toast', 'Interview dibatalkan.');
    }
}
