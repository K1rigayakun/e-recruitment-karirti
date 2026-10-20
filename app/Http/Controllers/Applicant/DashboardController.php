<?php

namespace App\Http\Controllers\Applicant;

use App\Http\Controllers\Controller;
use App\Models\Application;
use App\Services\ProfileCompleteness;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(Request $request, ProfileCompleteness $completeness): View
    {
        $user = $request->user();
        $profile = $user->applicantProfile->load(['skills', 'portfolioProjects', 'currentCv']);
        $progress = $completeness->calculate($profile);

        // Lamaran terbaru + ringkasan
        $recentApplications = Application::where('applicant_profile_id', $profile->id)
            ->with('jobPosting.companyProfile')
            ->latest()
            ->take(3)
            ->get();

        $applicationCounts = Application::where('applicant_profile_id', $profile->id)
            ->selectRaw('status, count(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        return view('applicant.dashboard', compact('user', 'profile', 'progress', 'recentApplications', 'applicationCounts'));
    }
}
