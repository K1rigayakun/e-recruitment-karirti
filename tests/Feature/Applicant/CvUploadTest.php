<?php

namespace Tests\Feature\Applicant;

use App\Enums\UserRole;
use App\Models\ApplicantProfile;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class CvUploadTest extends TestCase
{
    use RefreshDatabase;

    private function makeApplicant(): User
    {
        Model::unguard();
        $user = User::factory()->create([
            'role' => UserRole::Applicant,
            'email_verified_at' => now(),
        ]);
        $profile = new ApplicantProfile(['slug' => 'test-'.rand()]);
        $profile->user_id = $user->id;
        $profile->save();
        Model::reguard();

        return $user;
    }

    public function test_valid_pdf_upload_succeeds(): void
    {
        Storage::fake('local');
        $user = $this->makeApplicant();

        // File dengan magic bytes PDF yang benar
        $content = '%PDF-1.4 valid content';
        $file = UploadedFile::fake()->createWithContent('cv.pdf', $content);

        $response = $this->actingAs($user)
            ->post(route('applicant.cv.store'), ['cv' => $file]);

        $response->assertRedirect();
        Storage::disk('local')->assertExists(
            $user->applicantProfile->fresh()->currentCv->path
        );
    }

    public function test_txt_named_pdf_is_rejected(): void
    {
        Storage::fake('local');
        $user = $this->makeApplicant();

        // File teks biasa bernama .pdf — magic bytes tidak cocok
        $file = UploadedFile::fake()->createWithContent('malicious.pdf', 'plain text content');

        $this->actingAs($user)
            ->post(route('applicant.cv.store'), ['cv' => $file])
            ->assertSessionHasErrors('cv');
    }

    public function test_non_pdf_mime_is_rejected(): void
    {
        Storage::fake('local');
        $user = $this->makeApplicant();

        // File binary bukan PDF (header PNG) bernama .pdf
        $pngHeader = "\x89PNG\r\n\x1a\n fake png content";
        $file = UploadedFile::fake()->createWithContent('photo.pdf', $pngHeader);

        $this->actingAs($user)
            ->post(route('applicant.cv.store'), ['cv' => $file])
            ->assertSessionHasErrors('cv');
    }

    public function test_file_exceeding_size_limit_is_rejected(): void
    {
        Storage::fake('local');
        $user = $this->makeApplicant();

        // File melebihi 2048 KB
        $file = UploadedFile::fake()->create('cv.pdf', 3000, 'application/pdf');

        $this->actingAs($user)
            ->post(route('applicant.cv.store'), ['cv' => $file])
            ->assertSessionHasErrors('cv');
    }

    public function test_document_idor_returns_403(): void
    {
        Storage::fake('local');
        $owner = $this->makeApplicant();
        $other = $this->makeApplicant();

        // Upload CV milik owner
        $content = '%PDF-1.4 valid';
        $file = UploadedFile::fake()->createWithContent('cv.pdf', $content);
        $this->actingAs($owner)->post(route('applicant.cv.store'), ['cv' => $file]);

        $document = $owner->documents()->first();

        // other tidak boleh akses
        $this->actingAs($other)
            ->get(route('documents.show', $document))
            ->assertForbidden();
    }
}
