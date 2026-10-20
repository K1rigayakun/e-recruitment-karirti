<?php

namespace App\Http\Controllers\Admin;

use App\Enums\JobStatus;
use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\JobPosting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class JobModerationController extends Controller
{
    public function index(Request $request): View
    {
        $jobs = JobPosting::with('companyProfile')
            ->when($request->get('status'), fn ($q, $s) => $q->where('status', $s))
            ->latest()
            ->paginate(20)->withQueryString();

        return view('admin.jobs.index', compact('jobs'));
    }

    /** Turunkan lowongan: closed + admin_note wajib + audit log */
    public function takedown(Request $request, JobPosting $job): RedirectResponse
    {
        $request->validate(['note' => ['required', 'string', 'max:500']]);

        $job->status = JobStatus::Closed;
        $job->closed_at = now();
        $job->admin_note = $request->note;
        $job->save();

        AuditLog::create([
            'user_id' => $request->user()->id,
            'action' => 'job.takendown',
            'subject_type' => JobPosting::class,
            'subject_id' => $job->id,
            'meta' => ['note' => $request->note],
            'ip' => $request->ip(),
            'created_at' => now(),
        ]);

        return back()->with('toast', "Lowongan \"{$job->title}\" berhasil diturunkan.");
    }
}
