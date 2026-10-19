<?php

namespace App\Http\Controllers\Applicant;

use App\Http\Controllers\Controller;
use App\Http\Requests\Applicant\UpdateApplicantProfileRequest;
use App\Models\Skill;
use App\Services\ProfileCompleteness;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProfileController extends Controller
{
    public function edit(Request $request, ProfileCompleteness $completeness): View
    {
        $profile = $request->user()->applicantProfile->load('skills');
        $skills = Skill::orderBy('name')->get();
        $progress = $completeness->calculate($profile);

        return view('applicant.profile.edit', compact('profile', 'skills', 'progress'));
    }

    public function update(UpdateApplicantProfileRequest $request): RedirectResponse
    {
        $profile = $request->user()->applicantProfile;
        $data = $request->validated();

        $profile->fill(collect($data)->except('skills', 'is_public')->toArray());
        $profile->is_public = $request->boolean('is_public');
        $profile->save();

        // Sync skill
        if (isset($data['skills'])) {
            $profile->skills()->sync($data['skills']);
        } else {
            $profile->skills()->detach();
        }

        return back()->with('toast', 'Profil berhasil disimpan.');
    }
}
