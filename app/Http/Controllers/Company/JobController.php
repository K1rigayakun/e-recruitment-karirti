<?php

namespace App\Http\Controllers\Company;

use App\Enums\JobStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Company\StoreJobRequest;
use App\Http\Requests\Company\UpdateJobRequest;
use App\Models\JobPosting;
use App\Models\Skill;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class JobController extends Controller
{
    public function index(Request $request): View
    {
        $profile = $request->user()->companyProfile;

        $jobs = JobPosting::where('company_profile_id', $profile->id)
            ->withCount('applications')
            ->when($request->get('status'), fn ($q, $s) => $q->where('status', $s))
            ->latest()
            ->paginate(15)->withQueryString();

        return view('company.jobs.index', compact('jobs'));
    }

    public function create(): View
    {
        $skills = Skill::orderBy('name')->get();

        return view('company.jobs.create', compact('skills'));
    }

    public function store(StoreJobRequest $request): RedirectResponse
    {
        $profile = $request->user()->companyProfile;
        $data = $request->validated();

        // Slug unik dari judul
        $slug = $this->uniqueSlug(Str::slug($data['title']));

        $job = new JobPosting(collect($data)->except('skills')->toArray());
        $job->company_profile_id = $profile->id;
        $job->slug = $slug;
        $job->status = JobStatus::Draft;
        $job->save();

        if (! empty($data['skills'])) {
            $job->skills()->sync($data['skills']);
        }

        return redirect()->route('company.jobs.index')
            ->with('toast', 'Lowongan berhasil disimpan sebagai draft.');
    }

    public function edit(Request $request, JobPosting $job): View
    {
        $this->authorize('update', $job);
        $skills = Skill::orderBy('name')->get();

        return view('company.jobs.edit', compact('job', 'skills'));
    }

    public function update(UpdateJobRequest $request, JobPosting $job): RedirectResponse
    {
        $this->authorize('update', $job);
        $data = $request->validated();

        $job->fill(collect($data)->except('skills')->toArray());
        $job->save();

        if (isset($data['skills'])) {
            $job->skills()->sync($data['skills']);
        }

        return redirect()->route('company.jobs.index')
            ->with('toast', 'Lowongan berhasil diperbarui.');
    }

    public function destroy(Request $request, JobPosting $job): RedirectResponse
    {
        $this->authorize('delete', $job);
        $job->delete();

        return back()->with('toast', 'Lowongan berhasil dihapus.');
    }

    /** Publikasi lowongan — hanya perusahaan approved */
    public function publish(Request $request, JobPosting $job): RedirectResponse
    {
        $this->authorize('publish', $job);

        $job->status = JobStatus::Open;
        $job->published_at = now();
        $job->save();

        return back()->with('toast', 'Lowongan berhasil dipublikasikan.');
    }

    /** Tutup lowongan */
    public function close(Request $request, JobPosting $job): RedirectResponse
    {
        $this->authorize('close', $job);

        $job->status = JobStatus::Closed;
        $job->closed_at = now();
        $job->save();

        return back()->with('toast', 'Lowongan ditutup.');
    }

    /** Buat slug unik — tambah sufiks jika ada bentrok */
    private function uniqueSlug(string $base): string
    {
        $slug = $base;
        $suffix = 1;

        while (JobPosting::where('slug', $slug)->exists()) {
            $slug = $base.'-'.$suffix++;
        }

        return $slug;
    }
}
