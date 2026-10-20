<?php

namespace App\Http\Middleware;

use App\Enums\CompanyVerificationStatus;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Hanya izinkan perusahaan yang sudah diverifikasi admin.
 * Dipakai di rute yang membutuhkan perusahaan approved (posting lowongan, dll).
 */
class EnsureCompanyApproved
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();
        $profile = $user?->companyProfile;

        if (! $profile || $profile->verification_status !== CompanyVerificationStatus::Approved) {
            abort(403, 'Akun perusahaan belum diverifikasi.');
        }

        return $next($request);
    }
}
