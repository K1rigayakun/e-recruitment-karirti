<?php

namespace App\Http\Controllers\Applicant;

use App\Http\Controllers\Controller;
use App\Http\Requests\Applicant\RespondInterviewRequest;
use App\Models\Interview;
use App\Notifications\InterviewConfirmed;
use App\Notifications\InterviewRescheduleRequested;
use App\Services\InterviewScheduler;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class InterviewController extends Controller
{
    public function __construct(private readonly InterviewScheduler $scheduler) {}

    /** Konfirmasi hadir */
    public function confirm(Request $request, Interview $interview): RedirectResponse
    {
        $this->scheduler->confirm($interview, $request->user());

        // Notifikasi ke perusahaan
        $interview->application->jobPosting->companyProfile->user
            ->notify(new InterviewConfirmed($interview));

        return back()->with('toast', 'Kehadiran berhasil dikonfirmasi.');
    }

    /** Ajukan jadwal ulang */
    public function requestReschedule(RespondInterviewRequest $request, Interview $interview): RedirectResponse
    {
        $this->scheduler->requestReschedule(
            $interview,
            $request->user(),
            $request->validated('note')
        );

        // Notifikasi ke perusahaan
        $interview->application->jobPosting->companyProfile->user
            ->notify(new InterviewRescheduleRequested($interview));

        return back()->with('toast', 'Permintaan jadwal ulang berhasil dikirim.');
    }
}
