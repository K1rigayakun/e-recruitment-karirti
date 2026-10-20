<?php

namespace App\Http\Controllers\Auth;

use App\Enums\CompanyVerificationStatus;
use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\RegisterCompanyRequest;
use App\Models\CompanyProfile;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\View\View;

class RegisteredCompanyController extends Controller
{
    public function create(): View
    {
        return view('auth.register-company');
    }

    public function store(RegisterCompanyRequest $request): RedirectResponse
    {
        $user = DB::transaction(function () use ($request): User {
            $user = new User($request->only('name', 'email', 'password'));
            $user->role = UserRole::Company;
            $user->is_active = true;
            $user->save();

            $slug = Str::slug($request->company_name).'-'.Str::lower(Str::random(5));
            $profile = new CompanyProfile([
                'slug' => $slug,
                'company_name' => $request->company_name,
            ]);
            $profile->user_id = $user->id;
            $profile->verification_status = CompanyVerificationStatus::Pending;
            $profile->save();

            return $user;
        });

        event(new Registered($user));
        Auth::login($user);

        return redirect()->route('verification.notice');
    }
}
