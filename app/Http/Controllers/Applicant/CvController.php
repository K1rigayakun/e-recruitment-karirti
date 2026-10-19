<?php

namespace App\Http\Controllers\Applicant;

use App\Http\Controllers\Controller;
use App\Http\Requests\Applicant\StoreCvRequest;
use App\Models\Document;
use App\Services\DocumentService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CvController extends Controller
{
    public function __construct(private readonly DocumentService $documentService) {}

    public function index(Request $request): View
    {
        $documents = $request->user()->documents()->where('type', 'cv')->latest()->get();
        $profile = $request->user()->applicantProfile;

        return view('applicant.cv.index', compact('documents', 'profile'));
    }

    public function store(StoreCvRequest $request): RedirectResponse
    {
        $this->documentService->storeCv($request->user(), $request->file('cv'));

        return back()->with('toast', 'CV berhasil diunggah dan dijadikan aktif.');
    }

    public function setActive(Request $request, Document $document): RedirectResponse
    {
        // Hanya pemilik yang boleh
        abort_if($document->user_id !== $request->user()->id, 403);

        $profile = $request->user()->applicantProfile;
        $profile->current_cv_document_id = $document->id;
        $profile->save();

        return back()->with('toast', 'CV aktif diubah.');
    }

    public function destroy(Request $request, Document $document): RedirectResponse
    {
        abort_if($document->user_id !== $request->user()->id, 403);

        $error = $this->documentService->deleteDocument($document);

        if ($error) {
            return back()->withErrors(['cv' => $error]);
        }

        return back()->with('toast', 'CV berhasil dihapus.');
    }
}
