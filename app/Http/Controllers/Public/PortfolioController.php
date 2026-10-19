<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\ApplicantProfile;
use Illuminate\View\View;

class PortfolioController extends Controller
{
    public function show(string $slug): View
    {
        $profile = ApplicantProfile::with(['skills', 'portfolioProjects'])
            ->where('slug', $slug)
            ->where('is_public', true)
            ->firstOrFail();

        return view('public.portfolio.show', compact('profile'));
    }
}
