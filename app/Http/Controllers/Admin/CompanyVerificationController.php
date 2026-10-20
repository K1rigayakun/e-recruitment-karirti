<?php

namespace App\Http\Controllers\Admin;

use App\Enums\CompanyVerificationStatus;
use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\CompanyProfile;
use App\Notifications\CompanyVerified;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CompanyVerificationController extends Controller
{
    public function index(): View
    {
        $pending = CompanyProfile::with('user')
            ->where('verification_status', CompanyVerificationStatus::Pending)
            ->latest()
            ->paginate(15);

        return view('admin.companies.index', compact('pending'));
    }

    public function approve(Request $request, CompanyProfile $company): RedirectResponse
    {
        $company->verification_status = CompanyVerificationStatus::Approved;
        $company->verified_at = now();
        $company->verification_note = null;
        $company->save();

        AuditLog::create([
            'user_id' => $request->user()->id,
            'action' => 'company.approved',
            'subject_type' => CompanyProfile::class,
            'subject_id' => $company->id,
            'ip' => $request->ip(),
            'created_at' => now(),
        ]);

        // Notifikasi ke perusahaan
        $company->user->notify(new CompanyVerified($company, true));

        return back()->with('toast', "{$company->company_name} berhasil diverifikasi.");
    }

    public function reject(Request $request, CompanyProfile $company): RedirectResponse
    {
        $request->validate(['note' => ['required', 'string', 'max:500']]);

        $company->verification_status = CompanyVerificationStatus::Rejected;
        $company->verification_note = $request->note;
        $company->save();

        AuditLog::create([
            'user_id' => $request->user()->id,
            'action' => 'company.rejected',
            'subject_type' => CompanyProfile::class,
            'subject_id' => $company->id,
            'meta' => ['note' => $request->note],
            'ip' => $request->ip(),
            'created_at' => now(),
        ]);

        $company->user->notify(new CompanyVerified($company, false, $request->note));

        return back()->with('toast', "{$company->company_name} ditolak.");
    }
}
