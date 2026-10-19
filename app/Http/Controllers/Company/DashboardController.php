<?php

namespace App\Http\Controllers\Company;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(Request $request): View
    {
        $profile = $request->user()->companyProfile;

        return view('company.dashboard', [
            'user' => $request->user(),
            'profile' => $profile,
        ]);
    }
}
