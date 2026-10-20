<?php

namespace App\Http\Controllers\Company;

use App\Http\Controllers\Controller;
use App\Http\Requests\Company\UpdateCompanyProfileRequest;
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

        return back()->with('toast', 'Profil perusahaan berhasil disimpan.');
    }
}
