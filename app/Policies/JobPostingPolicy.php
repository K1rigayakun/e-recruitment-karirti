<?php

namespace App\Policies;

use App\Enums\CompanyVerificationStatus;
use App\Enums\JobStatus;
use App\Enums\UserRole;
use App\Models\JobPosting;
use App\Models\User;

class JobPostingPolicy
{
    /** Perusahaan pemilik atau admin bisa lihat (termasuk draft) */
    public function view(User $user, JobPosting $job): bool
    {
        if ($user->role === UserRole::Admin) {
            return true;
        }

        return $user->companyProfile?->id === $job->company_profile_id;
    }

    /** Hanya pemilik yang bisa edit */
    public function update(User $user, JobPosting $job): bool
    {
        return $user->companyProfile?->id === $job->company_profile_id;
    }

    /** Hanya draft tanpa lamaran yang bisa dihapus */
    public function delete(User $user, JobPosting $job): bool
    {
        if ($user->companyProfile?->id !== $job->company_profile_id) {
            return false;
        }

        return $job->status === JobStatus::Draft && ! $job->applications()->exists();
    }

    /** Hanya perusahaan approved yang bisa publikasi (BR9) */
    public function publish(User $user, JobPosting $job): bool
    {
        if ($user->companyProfile?->id !== $job->company_profile_id) {
            return false;
        }

        return $user->companyProfile?->verification_status === CompanyVerificationStatus::Approved;
    }

    /** Pemilik bisa tutup lowongan yang sedang open */
    public function close(User $user, JobPosting $job): bool
    {
        return $user->companyProfile?->id === $job->company_profile_id
            && $job->status === JobStatus::Open;
    }
}
