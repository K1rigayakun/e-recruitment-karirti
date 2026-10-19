<?php

namespace App\Http\Controllers\Company;

use App\Enums\ApplicationStatus;
use App\Enums\JobStatus;
use App\Http\Controllers\Controller;
use App\Models\Application;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();
        $profile = $user->companyProfile;

        $stats = [];
        if ($profile) {
            $stats = [
                'jobs_open' => $profile->jobPostings()->where('status', JobStatus::Open)->count(),
                'applicants' => Application::forCompany($profile->id)->count(),
                'pending' => Application::forCompany($profile->id)
                    ->whereIn('status', [ApplicationStatus::Applied->value, ApplicationStatus::Viewed->value])
                    ->count(),
            ];
        }

        return view('company.dashboard', compact('user', 'profile', 'stats'));
    }
}
