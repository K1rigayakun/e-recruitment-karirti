<?php

namespace App\Services;

use App\Enums\ApplicationStatus;
use App\Enums\UserRole;
use App\Models\Application;
use App\Models\User;
use App\Notifications\ApplicationStatusChanged;
use Illuminate\Support\Facades\DB;

/**
 * Semua perubahan status lamaran WAJIB melalui service ini.
 * Menjamin atomisitas (transaksi + lock), validasi matriks transisi,
 * pencatatan history, dan notifikasi.
 */
class ApplicationStatusService
{
    /**
     * @param  bool  $fromScheduler  Harus true jika dipanggil dari InterviewScheduler (BR7)
     *
     * @throws \DomainException jika transisi tidak valid
     */
    public function transition(
        Application $application,
        ApplicationStatus $to,
        User $actor,
        ?string $note = null,
        bool $fromScheduler = false
    ): Application {
        $from = $application->status;

        // Validasi matriks transisi (plan.md 7.4)
        if (! $from->canMoveTo($to)) {
            throw new \DomainException(
                "Transisi dari {$from->label()} ke {$to->label()} tidak diizinkan."
            );
        }

        // Transisi ke interview HANYA boleh dari InterviewScheduler (BR7)
        if ($to === ApplicationStatus::Interview && ! $fromScheduler) {
            throw new \DomainException('Transisi ke Interview hanya boleh lewat penjadwalan interview.');
        }

        // Validasi aktor sesuai matriks (plan.md 7.4)
        $this->assertActorAllowed($actor, $to, $application);

        DB::transaction(function () use ($application, $from, $to, $actor, $note): void {
            // Lock row agar tidak ada race condition
            $application = Application::lockForUpdate()->find($application->id);

            $application->status = $to;

            if ($to === ApplicationStatus::Viewed && is_null($application->viewed_at)) {
                $application->viewed_at = now();
            }

            if (in_array($to, [ApplicationStatus::Accepted, ApplicationStatus::Rejected], true)) {
                $application->decided_at = now();
            }

            $application->save();

            // Catat history
            $application->statusHistories()->create([
                'from_status' => $from->value,
                'to_status' => $to->value,
                'note' => $note,
                'changed_by' => $actor->id,
                'created_at' => now(),
            ]);
        });

        // Notifikasi ke pelamar di luar transaksi (queue)
        if ($to !== ApplicationStatus::Applied) {
            $applicantUser = $application->applicantProfile->user;
            if ($applicantUser) {
                $applicantUser->notify(new ApplicationStatusChanged($application->fresh()));
            }
        }

        return $application->fresh();
    }

    private function assertActorAllowed(User $actor, ApplicationStatus $to, Application $application): void
    {
        // Pelamar hanya bisa withdraw
        if ($actor->role === UserRole::Applicant) {
            if ($to !== ApplicationStatus::Withdrawn) {
                throw new \DomainException('Pelamar hanya bisa membatalkan lamaran.');
            }
            if ($application->applicantProfile->user_id !== $actor->id) {
                throw new \DomainException('Kamu tidak punya akses ke lamaran ini.');
            }

            return;
        }

        // Perusahaan bisa accepted, rejected, viewed, interview — tapi hanya ke lowongannya sendiri
        if ($actor->role === UserRole::Company) {
            if (! in_array($to, [
                ApplicationStatus::Viewed,
                ApplicationStatus::Interview,
                ApplicationStatus::Accepted,
                ApplicationStatus::Rejected,
            ], true)) {
                throw new \DomainException('Perusahaan tidak bisa melakukan transisi ini.');
            }
            if ($application->jobPosting->companyProfile->user_id !== $actor->id) {
                throw new \DomainException('Kamu tidak punya akses ke lamaran ini.');
            }
        }
    }
}
