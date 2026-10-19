<?php

use App\Http\Controllers\Admin\DashboardController as AdminDashboard;
use App\Http\Controllers\Applicant\DashboardController as ApplicantDashboard;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\EmailVerificationNotificationController;
use App\Http\Controllers\Auth\EmailVerificationPromptController;
use App\Http\Controllers\Auth\NewPasswordController;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use App\Http\Controllers\Auth\RegisteredApplicantController;
use App\Http\Controllers\Auth\RegisteredCompanyController;
use App\Http\Controllers\Auth\VerifyEmailController;
use App\Http\Controllers\Company\DashboardController as CompanyDashboard;
use Illuminate\Support\Facades\Route;

// ── Halaman publik ────────────────────────────────────────────────────────────
Route::get('/', fn () => view('welcome'))->name('home');

// ── Auth: tamu saja ───────────────────────────────────────────────────────────
Route::middleware('guest')->group(function (): void {
    Route::get('/masuk', [AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('/masuk', [AuthenticatedSessionController::class, 'store']);

    Route::get('/daftar/pelamar', [RegisteredApplicantController::class, 'create'])->name('register.applicant');
    Route::post('/daftar/pelamar', [RegisteredApplicantController::class, 'store']);

    Route::get('/daftar/perusahaan', [RegisteredCompanyController::class, 'create'])->name('register.company');
    Route::post('/daftar/perusahaan', [RegisteredCompanyController::class, 'store']);

    Route::get('/lupa-password', [PasswordResetLinkController::class, 'create'])->name('password.request');
    Route::post('/lupa-password', [PasswordResetLinkController::class, 'store'])->name('password.email');

    Route::get('/reset-password/{token}', [NewPasswordController::class, 'create'])->name('password.reset');
    Route::post('/reset-password', [NewPasswordController::class, 'store'])->name('password.store');
});

// ── Auth: sudah login ─────────────────────────────────────────────────────────
Route::middleware('auth')->group(function (): void {
    Route::post('/keluar', [AuthenticatedSessionController::class, 'destroy'])->name('logout');

    // Verifikasi email
    Route::get('/verifikasi-email', EmailVerificationPromptController::class)->name('verification.notice');
    Route::get('/verifikasi-email/{id}/{hash}', VerifyEmailController::class)
        ->middleware(['signed', 'throttle:6,1'])
        ->name('verification.verify');
    Route::post('/verifikasi-email/kirim-ulang', [EmailVerificationNotificationController::class, 'store'])
        ->middleware('throttle:6,1')
        ->name('verification.send');
});

// ── Dashboard pelamar ─────────────────────────────────────────────────────────
Route::middleware(['auth', 'verified', 'active', 'role:applicant'])
    ->prefix('/pelamar')
    ->name('applicant.')
    ->group(function (): void {
        Route::get('/', [ApplicantDashboard::class, 'index'])->name('dashboard');
    });

// ── Dashboard perusahaan ──────────────────────────────────────────────────────
Route::middleware(['auth', 'verified', 'active', 'role:company'])
    ->prefix('/perusahaan')
    ->name('company.')
    ->group(function (): void {
        Route::get('/', [CompanyDashboard::class, 'index'])->name('dashboard');
    });

// ── Dashboard admin ───────────────────────────────────────────────────────────
Route::middleware(['auth', 'verified', 'active', 'role:admin'])
    ->prefix('/admin')
    ->name('admin.')
    ->group(function (): void {
        Route::get('/', [AdminDashboard::class, 'index'])->name('dashboard');
    });

// ── Styleguide (hanya local) ──────────────────────────────────────────────────
Route::get('/styleguide', function () {
    abort_unless(app()->environment('local'), 404);

    return view('styleguide.index');
})->name('styleguide');
Route::get('/styleguide/layout/app', function () {
    abort_unless(app()->environment('local'), 404);

    return view('styleguide.layout-app');
})->name('styleguide.layout.app');
Route::get('/styleguide/layout/guest', function () {
    abort_unless(app()->environment('local'), 404);

    return view('styleguide.layout-guest');
})->name('styleguide.layout.guest');
Route::get('/styleguide/layout/panel', function () {
    abort_unless(app()->environment('local'), 404);

    return view('styleguide.layout-panel');
})->name('styleguide.layout.panel');
