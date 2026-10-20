<?php

namespace App\Policies;

use App\Models\PortfolioProject;
use App\Models\User;

class PortfolioProjectPolicy
{
    public function view(User $user, PortfolioProject $project): bool
    {
        return $user->applicantProfile?->id === $project->applicant_profile_id;
    }

    public function update(User $user, PortfolioProject $project): bool
    {
        return $user->applicantProfile?->id === $project->applicant_profile_id;
    }

    public function delete(User $user, PortfolioProject $project): bool
    {
        return $user->applicantProfile?->id === $project->applicant_profile_id;
    }
}
