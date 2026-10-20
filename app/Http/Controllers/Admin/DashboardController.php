<?php

namespace App\Http\Controllers\Admin;

use App\Enums\CompanyVerificationStatus;
use App\Enums\JobStatus;
use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Models\Application;
use App\Models\CompanyProfile;
use App\Models\JobPosting;
use App\Models\User;
use App\Services\ReportBuilder;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(Request $request, ReportBuilder $report): View
    {
        $stats = [
            'users_applicant' => User::where('role', UserRole::Applicant->value)->count(),
            'users_company' => User::where('role', UserRole::Company->value)->count(),
            'pending' => CompanyProfile::where('verification_status', CompanyVerificationStatus::Pending)->count(),
            'jobs_open' => JobPosting::where('status', JobStatus::Open)->count(),
            'applications' => Application::count(),
            'accepted' => Application::where('status', 'accepted')->count(),
        ];

        $trend = $report->monthlyTrend();

        return view('admin.dashboard', compact('stats', 'trend'));
    }
}
