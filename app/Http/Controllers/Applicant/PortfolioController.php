<?php

namespace App\Http\Controllers\Applicant;

use App\Http\Controllers\Controller;
use App\Http\Requests\Applicant\StorePortfolioProjectRequest;
use App\Http\Requests\Applicant\UpdatePortfolioProjectRequest;
use App\Models\PortfolioProject;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PortfolioController extends Controller
{
    public function index(Request $request): View
    {
        $profile = $request->user()->applicantProfile;
        $projects = $profile->portfolioProjects;

        return view('applicant.portfolio.index', compact('projects'));
    }

    public function create(): View
    {
        return view('applicant.portfolio.create');
    }

    public function store(StorePortfolioProjectRequest $request): RedirectResponse
    {
        $profile = $request->user()->applicantProfile;

        // BR10: maksimal 6 proyek
        if ($profile->portfolioProjects()->count() >= config('karirti.max_projects', 6)) {
            return back()->withErrors(['limit' => 'Maksimal '.config('karirti.max_projects', 6).' project portofolio.']);
        }

        $maxOrder = $profile->portfolioProjects()->max('sort_order') ?? -1;

        $project = new PortfolioProject($request->validated());
        $project->applicant_profile_id = $profile->id;
        $project->sort_order = $maxOrder + 1;
        $project->save();

        return redirect()->route('applicant.projects.index')->with('toast', 'Project berhasil ditambahkan.');
    }

    public function edit(PortfolioProject $project): View
    {
        $this->authorize('update', $project);

        return view('applicant.portfolio.edit', compact('project'));
    }

    public function update(UpdatePortfolioProjectRequest $request, PortfolioProject $project): RedirectResponse
    {
        $this->authorize('update', $project);
        $project->fill($request->validated());
        $project->save();

        return redirect()->route('applicant.projects.index')->with('toast', 'Project berhasil diperbarui.');
    }

    public function destroy(Request $request, PortfolioProject $project): RedirectResponse
    {
        $this->authorize('delete', $project);
        $project->delete();

        return back()->with('toast', 'Project berhasil dihapus.');
    }

    /** Pindah urutan ke atas */
    public function moveUp(Request $request, PortfolioProject $project): RedirectResponse
    {
        $this->authorize('update', $project);
        $profile = $request->user()->applicantProfile;

        $prev = $profile->portfolioProjects()
            ->where('sort_order', '<', $project->sort_order)
            ->orderByDesc('sort_order')
            ->first();

        if ($prev) {
            [$project->sort_order, $prev->sort_order] = [$prev->sort_order, $project->sort_order];
            $project->save();
            $prev->save();
        }

        return back();
    }

    /** Pindah urutan ke bawah */
    public function moveDown(Request $request, PortfolioProject $project): RedirectResponse
    {
        $this->authorize('update', $project);
        $profile = $request->user()->applicantProfile;

        $next = $profile->portfolioProjects()
            ->where('sort_order', '>', $project->sort_order)
            ->orderBy('sort_order')
            ->first();

        if ($next) {
            [$project->sort_order, $next->sort_order] = [$next->sort_order, $project->sort_order];
            $project->save();
            $next->save();
        }

        return back();
    }
}
