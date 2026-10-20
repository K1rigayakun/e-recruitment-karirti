<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Auth\Events\Verified;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\RedirectResponse;

class VerifyEmailController extends Controller
{
    public function __invoke(EmailVerificationRequest $request): RedirectResponse
    {
        if ($request->user()->hasVerifiedEmail()) {
            return redirect()->route('applicant.dashboard')->with('status', 'verified');
        }

        if ($request->user()->markEmailAsVerified()) {
            event(new Verified($request->user()));
        }

        $route = match ($request->user()->role->value) {
            'admin' => route('admin.dashboard'),
            'company' => route('company.dashboard'),
            default => route('applicant.dashboard'),
        };

        return redirect($route)->with('status', 'verified');
    }
}
