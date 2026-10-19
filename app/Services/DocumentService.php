<?php

namespace App\Services;

use App\Models\Document;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class DocumentService
{
    /**
     * Simpan CV ke disk private, catat Document, jadikan CV aktif.
     * Disk "local" = storage/app (tidak bisa diakses publik secara langsung).
     */
    public function storeCv(User $user, UploadedFile $file): Document
    {
        $size = $file->getSize();
        $hash = hash_file('sha256', $file->getRealPath());

        // Nama file: UUID.pdf — nama asli TIDAK dipakai sebagai path (keamanan)
        $path = $file->storeAs("cv/{$user->id}", Str::uuid()->toString().'.pdf', 'local');

        // Nama asli disanitasi hanya untuk label tampilan
        $baseName = Str::limit(
            Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME)) ?: 'cv',
            80,
            ''
        );

        $document = new Document([
            'type' => 'cv',
            'original_name' => $baseName.'.pdf',
            'path' => $path,
            'mime_type' => 'application/pdf',
            'size_bytes' => $size,
            'sha256' => $hash,
        ]);
        $document->user_id = $user->id;
        $document->save();

        // Jadikan CV aktif
        $profile = $user->applicantProfile;
        $profile->current_cv_document_id = $document->id;
        $profile->save();

        return $document;
    }

    /**
     * Hapus dokumen — hanya jika tidak dipakai oleh lamaran manapun.
     * Kembalikan pesan error jika tidak bisa dihapus.
     */
    public function deleteDocument(Document $document): ?string
    {
        if ($document->applications()->exists()) {
            return 'Dokumen tidak dapat dihapus karena digunakan oleh lamaran yang ada.';
        }

        // Jika ini CV aktif, lepaskan dulu
        $profile = $document->user->applicantProfile;
        if ($profile && $profile->current_cv_document_id === $document->id) {
            $profile->current_cv_document_id = null;
            $profile->save();
        }

        Storage::disk('local')->delete($document->path);
        $document->delete();

        return null;
    }
}
