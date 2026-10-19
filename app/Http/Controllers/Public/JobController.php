<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Http\Requests\Public\JobFilterRequest;
use App\Models\JobPosting;
use App\Models\Skill;
use Illuminate\View\View;

class JobController extends Controller
{
    public function index(JobFilterRequest $request): View
    {
        $query = JobPosting::open()->with(['companyProfile', 'skills']);

        if ($q = $request->validated('q')) {
            $query->search($q);
        }
        if ($type = $request->validated('type')) {
            $query->where('job_type', $type);
        }
        if ($mode = $request->validated('mode')) {
            $query->where('work_mode', $mode);
        }
        if ($location = $request->validated('location')) {
            $query->where('location', 'like', '%'.escapeLike($location).'%');
        }
        if ($skillIds = $request->validated('skill')) {
            $query->whereHas('skills', fn ($sq) => $sq->whereIn('skills.id', $skillIds));
        }

        $sort = $request->validated('sort', 'latest');
        $query = $sort === 'deadline'
            ? $query->orderByRaw('deadline_at IS NULL ASC')->orderBy('deadline_at')
            : $query->latest('published_at');

        $jobs = $query->paginate(12)->withQueryString();
        $skills = Skill::orderBy('name')->get();

        return view('public.jobs.index', compact('jobs', 'skills'));
    }

    public function show(string $slug): View
    {
        $job = JobPosting::with(['companyProfile', 'skills'])
            ->where('slug', $slug)
            ->firstOrFail();

        $user = auth()->user();
        $isOwner = $user && $user->companyProfile?->id === $job->company_profile_id;
        $isAdmin = $user?->role?->value === 'admin';

        if ($job->status->value !== 'open' && ! $isOwner && ! $isAdmin) {
            abort(404);
        }

        $skillIds = $job->skills->pluck('id');
        $similar = JobPosting::open()
            ->with(['companyProfile', 'skills'])
            ->where('id', '!=', $job->id)
            ->whereHas('skills', fn ($sq) => $sq->whereIn('skills.id', $skillIds))
            ->take(3)
            ->get();

        $isPreviewing = ($isOwner || $isAdmin) && $job->status->value !== 'open';

        return view('public.jobs.show', compact('job', 'similar', 'isPreviewing'));
    }
}
