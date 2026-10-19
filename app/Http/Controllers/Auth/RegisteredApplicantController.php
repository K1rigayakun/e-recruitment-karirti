<?php

namespace App\Http\Controllers\Auth;

use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\RegisterApplicantRequest;
use App\Models\ApplicantProfile;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\View\View;

class RegisteredApplicantController extends Controller
{
    public function create(): View
    {
        return view('auth.register-applicant');
    }

    public function store(RegisterApplicantRequest $request): RedirectResponse
    {
        $user = DB::transaction(function () use ($request): User {
            // Role di-set di controller, TIDAK dari input
            $user = new User($request->only('name', 'email', 'password'));
            $user->role = UserRole::Applicant;
            $user->is_active = true;
            $user->save();

            // Buat profil kosong — user_id di-set langsung karena bukan fillable
            $slug = Str::slug($user->name).'-'.Str::lower(Str::random(5));
            $profile = new ApplicantProfile([
                'slug' => $slug,
                'graduation_year' => $request->graduation_year,
                'nim' => $request->nim,
            ]);
            $profile->user_id = $user->id;
            $profile->save();

            return $user;
        });

        event(new Registered($user));
        Auth::login($user);

        return redirect()->route('verification.notice');
    }
}
