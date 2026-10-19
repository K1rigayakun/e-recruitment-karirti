<?php

namespace App\Http\Controllers\Applicant;

use App\Http\Controllers\Controller;
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

        return view('applicant.dashboard', compact('user', 'profile', 'progress'));
    }
}
