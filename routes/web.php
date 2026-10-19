<?php

use App\Http\Controllers\Admin\DashboardController as AdminDashboard;
use App\Http\Controllers\Applicant\CvController;
use App\Http\Controllers\Applicant\DashboardController as ApplicantDashboard;
use App\Http\Controllers\Applicant\PortfolioController;
use App\Http\Controllers\Applicant\ProfileController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\EmailVerificationNotificationController;
use App\Http\Controllers\Auth\EmailVerificationPromptController;
use App\Http\Controllers\Auth\NewPasswordController;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use App\Http\Controllers\Auth\RegisteredApplicantController;
use App\Http\Controllers\Auth\RegisteredCompanyController;
use App\Http\Controllers\Auth\VerifyEmailController;
use App\Http\Controllers\Company\DashboardController as CompanyDashboard;
use App\Http\Controllers\Company\JobController as CompanyJobController;
use App\Http\Controllers\Company\ProfileController as CompanyProfileController;
use App\Http\Controllers\DocumentController;
use App\Http\Controllers\Public\HomeController;
use App\Http\Controllers\Public\JobController;
use App\Http\Controllers\Public\PortfolioController as PublicPortfolioController;
use Illuminate\Support\Facades\Route;

// ── Halaman publik ────────────────────────────────────────────────────────────
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/lowongan', [JobController::class, 'index'])->name('jobs.index');
Route::get('/lowongan/{slug}', [JobController::class, 'show'])->name('jobs.show');
Route::get('/privasi', fn () => view('public.privacy'))->name('privacy');
Route::get('/p/{slug}', [PublicPortfolioController::class, 'show'])->name('portfolios.show');

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

    Route::get('/verifikasi-email', EmailVerificationPromptController::class)->name('verification.notice');
    Route::get('/verifikasi-email/{id}/{hash}', VerifyEmailController::class)
        ->middleware(['signed', 'throttle:6,1'])
        ->name('verification.verify');
    Route::post('/verifikasi-email/kirim-ulang', [EmailVerificationNotificationController::class, 'store'])
        ->middleware('throttle:6,1')
        ->name('verification.send');

    Route::get('/dokumen/{document}', [DocumentController::class, 'show'])->name('documents.show');
});

// ── Area pelamar ──────────────────────────────────────────────────────────────
Route::middleware(['auth', 'verified', 'active', 'role:applicant'])
    ->prefix('/pelamar')->name('applicant.')
    ->group(function (): void {
        Route::get('/', [ApplicantDashboard::class, 'index'])->name('dashboard');
        Route::get('/profil', [ProfileController::class, 'edit'])->name('profile.edit');
        Route::put('/profil', [ProfileController::class, 'update'])->name('profile.update');
        Route::get('/cv', [CvController::class, 'index'])->name('cv.index');
        Route::post('/cv', [CvController::class, 'store'])->name('cv.store');
        Route::patch('/cv/{document}/aktif', [CvController::class, 'setActive'])->name('cv.set-active');
        Route::delete('/cv/{document}', [CvController::class, 'destroy'])->name('cv.destroy');
        Route::get('/proyek', [PortfolioController::class, 'index'])->name('projects.index');
        Route::get('/proyek/tambah', [PortfolioController::class, 'create'])->name('projects.create');
        Route::post('/proyek', [PortfolioController::class, 'store'])->name('projects.store');
        Route::get('/proyek/{project}/edit', [PortfolioController::class, 'edit'])->name('projects.edit');
        Route::put('/proyek/{project}', [PortfolioController::class, 'update'])->name('projects.update');
        Route::delete('/proyek/{project}', [PortfolioController::class, 'destroy'])->name('projects.destroy');
        Route::post('/proyek/{project}/naik', [PortfolioController::class, 'moveUp'])->name('projects.move-up');
        Route::post('/proyek/{project}/turun', [PortfolioController::class, 'moveDown'])->name('projects.move-down');
    });

// ── Area perusahaan ───────────────────────────────────────────────────────────
Route::middleware(['auth', 'verified', 'active', 'role:company'])
    ->prefix('/perusahaan')->name('company.')
    ->group(function (): void {
        Route::get('/', [CompanyDashboard::class, 'index'])->name('dashboard');
        Route::get('/profil', [CompanyProfileController::class, 'edit'])->name('profile.edit');
        Route::put('/profil', [CompanyProfileController::class, 'update'])->name('profile.update');
        Route::get('/lowongan', [CompanyJobController::class, 'index'])->name('jobs.index');
        Route::get('/lowongan/buat', [CompanyJobController::class, 'create'])->name('jobs.create');
        Route::post('/lowongan', [CompanyJobController::class, 'store'])->name('jobs.store');
        Route::get('/lowongan/{job}/edit', [CompanyJobController::class, 'edit'])->name('jobs.edit');
        Route::put('/lowongan/{job}', [CompanyJobController::class, 'update'])->name('jobs.update');
        Route::delete('/lowongan/{job}', [CompanyJobController::class, 'destroy'])->name('jobs.destroy');
        Route::post('/lowongan/{job}/publikasi', [CompanyJobController::class, 'publish'])->name('jobs.publish');
        Route::post('/lowongan/{job}/tutup', [CompanyJobController::class, 'close'])->name('jobs.close');
    });

// ── Area admin ────────────────────────────────────────────────────────────────
Route::middleware(['auth', 'verified', 'active', 'role:admin'])
    ->prefix('/admin')->name('admin.')
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
