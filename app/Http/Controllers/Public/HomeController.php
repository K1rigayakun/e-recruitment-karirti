<?php

namespace App\Http\Controllers\Public;

use App\Enums\CompanyVerificationStatus;
use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Models\CompanyProfile;
use App\Models\JobPosting;
use App\Models\Skill;
use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function index(): View
    {
        // Statistik di-cache 10 menit agar tidak query setiap request
        $stats = Cache::remember('home.stats', 600, function () {
            return [
                'jobs' => JobPosting::open()->count(),
                'companies' => CompanyProfile::where('verification_status', CompanyVerificationStatus::Approved)->count(),
                'applicants' => User::where('role', UserRole::Applicant->value)->count(),
            ];
        });

        $latestJobs = JobPosting::open()
            ->with(['companyProfile', 'skills'])
            ->latest('published_at')
            ->take(6)
            ->get();

        $skills = Skill::all()->take(30);

        return view('public.home', compact('stats', 'latestJobs', 'skills'));
    }
}
