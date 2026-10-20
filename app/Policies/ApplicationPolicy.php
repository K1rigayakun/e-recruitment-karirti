<?php

namespace App\Policies;

use App\Enums\UserRole;
use App\Models\Application;
use App\Models\User;

class ApplicationPolicy
{
    /** Siapa yang boleh lihat detail lamaran */
    public function view(User $user, Application $application): bool
    {
        return match ($user->role) {
            UserRole::Applicant => $application->applicantProfile->user_id === $user->id,
            UserRole::Company => $application->jobPosting->companyProfile->user_id === $user->id,
            UserRole::Admin => true,
        };
    }

    /**
     * Hanya perusahaan pemilik lowongan yang bisa memutuskan.
     * Admin sengaja TIDAK bisa memutuskan (plan.md C.10).
     */
    public function decide(User $user, Application $application): bool
    {
        return $user->role === UserRole::Company
            && $application->jobPosting->companyProfile->user_id === $user->id;
    }

    /** Pelamar bisa withdraw selama status belum final */
    public function withdraw(User $user, Application $application): bool
    {
        return $user->role === UserRole::Applicant
            && $application->applicantProfile->user_id === $user->id
            && ! $application->status->isFinal();
    }
}
