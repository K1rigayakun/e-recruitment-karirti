<?php

namespace App\Http\Controllers\Company;

use App\Enums\CompanyVerificationStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Company\UpdateCompanyProfileRequest;
use App\Models\AuditLog;
use App\Models\CompanyProfile;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;

class ProfileController extends Controller
{
    public function edit(Request $request): View
    {
        $profile = $request->user()->companyProfile;

        return view('company.profile.edit', compact('profile'));
    }

    public function update(UpdateCompanyProfileRequest $request): RedirectResponse
    {
        $profile = $request->user()->companyProfile;
        $data = $request->validated();

        // Jika perusahaan terverifikasi mengubah nama atau website, kembalikan ke status pending
        $nameChanged = isset($data['company_name']) && $data['company_name'] !== $profile->company_name;
        $websiteChanged = array_key_exists('website', $data) && $data['website'] !== $profile->website;

        $reverificationTriggered = false;
        if ($profile->verification_status === CompanyVerificationStatus::Approved && ($nameChanged || $websiteChanged)) {
            $profile->verification_status = CompanyVerificationStatus::Pending;
            $profile->verified_at = null;
            $profile->verification_note = 'Identitas perusahaan diubah, membutuhkan verifikasi ulang oleh admin.';
            $reverificationTriggered = true;

            AuditLog::create([
                'user_id' => $request->user()->id,
                'action' => 'company.identity_changed_reverification',
                'subject_type' => CompanyProfile::class,
                'subject_id' => $profile->id,
                'ip' => $request->ip(),
                'created_at' => now(),
            ]);
        }

        // Logo upload: disk public, nama UUID, hapus lama
        if ($request->hasFile('logo')) {
            if ($profile->logo_path) {
                Storage::disk('public')->delete($profile->logo_path);
            }
            $path = $request->file('logo')->storeAs(
                'logos',
                Str::uuid()->toString().'.'.$request->file('logo')->extension(),
                'public'
            );
            $data['logo_path'] = $path;
        }

        $profile->fill(collect($data)->except('logo')->toArray());
        if (isset($data['logo_path'])) {
            $profile->logo_path = $data['logo_path'];
        }
        $profile->save();

        $message = $reverificationTriggered
            ? 'Profil perusahaan berhasil disimpan. Karena nama/website diubah, akun menunggu verifikasi ulang oleh admin.'
            : 'Profil perusahaan berhasil disimpan.';

        return back()->with('toast', $message);
    }
}
