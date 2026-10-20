<?php

namespace App\Services;

use App\Enums\ApplicationStatus;
use App\Enums\InterviewMode;
use App\Enums\InterviewStatus;
use App\Models\Application;
use App\Models\Interview;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

/**
 * Semua operasi interview dilakukan lewat service ini.
 * BR7: satu-satunya tempat yang boleh memindahkan lamaran ke status Interview.
 */
class InterviewScheduler
{
    public function __construct(private readonly ApplicationStatusService $statusService) {}

    /**
     * Jadwalkan interview baru.
     *
     * @param  array{scheduled_at: Carbon, duration_minutes: int, mode: string, meeting_url: ?string, location: ?string, instructions: ?string}  $data
     *
     * @throws \DomainException
     */
    public function schedule(Application $application, User $actor, array $data): Interview
    {
        $this->validateScheduleData($data);
        $this->assertNoConflict($application, $data['scheduled_at'], $data['duration_minutes']);

        return DB::transaction(function () use ($application, $actor, $data): Interview {
            // BR7: pindahkan lamaran ke interview lewat service ini
            if ($application->status !== ApplicationStatus::Interview) {
                $this->statusService->transition(
                    $application,
                    ApplicationStatus::Interview,
                    $actor,
                    null,
                    true // fromScheduler: hanya InterviewScheduler yang boleh (BR7)
                );
                $application->refresh();
            }

            $interview = new Interview([
                'application_id' => $application->id,
                'scheduled_at' => $data['scheduled_at'],
                'duration_minutes' => $data['duration_minutes'],
                'mode' => $data['mode'],
                'meeting_url' => $data['meeting_url'] ?? null,
                'location' => $data['location'] ?? null,
                'instructions' => $data['instructions'] ?? null,
                'created_by' => $actor->id,
            ]);
            $interview->status = InterviewStatus::Scheduled;
            $interview->save();

            return $interview;
        });
    }

    /**
     * Jadwal ulang interview yang sudah ada.
     *
     * @throws \DomainException
     */
    public function reschedule(Interview $interview, User $actor, array $data): Interview
    {
        $this->validateScheduleData($data);

        return DB::transaction(function () use ($interview, $data): Interview {
            $interview->scheduled_at = $data['scheduled_at'];
            $interview->duration_minutes = $data['duration_minutes'];
            $interview->mode = $data['mode'];
            $interview->meeting_url = $data['meeting_url'] ?? null;
            $interview->location = $data['location'] ?? null;
            $interview->instructions = $data['instructions'] ?? null;
            $interview->status = InterviewStatus::Scheduled;
            $interview->save();

            return $interview;
        });
    }

    /**
     * Batalkan interview.
     */
    public function cancel(Interview $interview, User $actor): Interview
    {
        $interview->status = InterviewStatus::Cancelled;
        $interview->save();

        return $interview;
    }

    /**
     * Pelamar konfirmasi hadir.
     */
    public function confirm(Interview $interview, User $actor): Interview
    {
        abort_unless($interview->application->applicantProfile->user_id === $actor->id, 403);

        $interview->status = InterviewStatus::Confirmed;
        $interview->save();

        return $interview;
    }

    /**
     * Pelamar ajukan jadwal ulang.
     */
    public function requestReschedule(Interview $interview, User $actor, ?string $note): Interview
    {
        abort_unless($interview->application->applicantProfile->user_id === $actor->id, 403);

        $interview->status = InterviewStatus::RescheduleRequested;
        $interview->applicant_note = $note;
        $interview->save();

        return $interview;
    }

    /**
     * Validasi data jadwal: waktu minimal 1 jam dari sekarang, online/onsite valid.
     *
     * @throws \DomainException
     */
    private function validateScheduleData(array $data): void
    {
        $scheduledAt = $data['scheduled_at'] instanceof Carbon
            ? $data['scheduled_at']
            : Carbon::parse($data['scheduled_at']);

        if ($scheduledAt->lt(now()->addHour())) {
            throw new \DomainException('Waktu interview minimal 1 jam dari sekarang.');
        }

        $mode = $data['mode'] instanceof InterviewMode
            ? $data['mode']
            : InterviewMode::from($data['mode']);

        if ($mode === InterviewMode::Online && empty($data['meeting_url'])) {
            throw new \DomainException('Interview online membutuhkan URL meeting (https).');
        }

        if ($mode === InterviewMode::Onsite && empty($data['location'])) {
            throw new \DomainException('Interview tatap muka membutuhkan lokasi.');
        }
    }

    /**
     * Cek bentrok interview dengan pelamar yang sama (BR11).
     *
     * @throws \DomainException
     */
    private function assertNoConflict(Application $application, Carbon $start, int $duration): void
    {
        $profileId = $application->applicant_profile_id;

        // Ambil semua interview aktif milik pelamar ini, cek tumpang tindih di PHP
        // (menghindari SQL non-portabel DATE_ADD untuk kompatibilitas SQLite di test)
        $activeInterviews = Interview::whereHas(
            'application',
            fn ($q) => $q->where('applicant_profile_id', $profileId)
        )
            ->whereIn('status', [
                InterviewStatus::Scheduled->value,
                InterviewStatus::Confirmed->value,
                InterviewStatus::RescheduleRequested->value,
            ])
            ->get();

        $newEnd = $start->copy()->addMinutes($duration);

        foreach ($activeInterviews as $existing) {
            $existingStart = Carbon::parse($existing->scheduled_at);
            $existingEnd = $existingStart->copy()->addMinutes($existing->duration_minutes);

            // Cek tumpang tindih: dua rentang [A,B) dan [C,D) tumpang tindih jika A < D && C < B
            if ($start->lt($existingEnd) && $existingStart->lt($newEnd)) {
                throw new \DomainException('Pelamar ini sudah memiliki interview aktif di waktu yang berdekatan.');
            }
        }
    }
}
