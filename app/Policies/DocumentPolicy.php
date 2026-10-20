<?php

namespace App\Policies;

use App\Enums\UserRole;
use App\Models\Document;
use App\Models\User;

class DocumentPolicy
{
    /**
     * Pemilik dokumen, admin, atau perusahaan yang punya lamaran
     * menggunakan dokumen tersebut boleh mengakses.
     */
    public function view(User $user, Document $document): bool
    {
        // Pemilik sendiri
        if ($user->id === $document->user_id) {
            return true;
        }

        // Admin
        if ($user->role === UserRole::Admin) {
            return true;
        }

        // Perusahaan yang punya lamaran menggunakan dokumen ini
        if ($user->role === UserRole::Company) {
            $companyProfile = $user->companyProfile;
            if (! $companyProfile) {
                return false;
            }

            return $document->applications()
                ->whereHas('jobPosting', fn ($q) => $q->where('company_profile_id', $companyProfile->id))
                ->exists();
        }

        return false;
    }

    public function delete(User $user, Document $document): bool
    {
        return $user->id === $document->user_id;
    }
}
