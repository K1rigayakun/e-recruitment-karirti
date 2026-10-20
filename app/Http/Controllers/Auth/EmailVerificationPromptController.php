<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class EmailVerificationPromptController extends Controller
{
    public function __invoke(Request $request): RedirectResponse|View
    {
        return $request->user()->hasVerifiedEmail()
            ? redirect()->intended($this->dashboardRoute($request))
            : view('auth.verify-email');
    }

    private function dashboardRoute(Request $request): string
    {
        return match ($request->user()->role->value) {
            'admin' => route('admin.dashboard'),
            'company' => route('company.dashboard'),
            default => route('applicant.dashboard'),
        };
    }
}
