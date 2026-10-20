<?php

namespace Tests\Feature\Applicant;

use App\Enums\ApplicationStatus;
use App\Enums\CompanyVerificationStatus;
use App\Enums\JobStatus;
use App\Enums\UserRole;
use App\Models\ApplicantProfile;
use App\Models\Application;
use App\Models\CompanyProfile;
use App\Models\Document;
use App\Models\JobPosting;
use App\Models\User;
use App\Notifications\ApplicationReceived;
use App\Services\ApplicationStatusService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class ApplicationTest extends TestCase
{
    use RefreshDatabase;

    private function makeApplicantWithCv(): array
    {
        Model::unguard();
        $user = User::factory()->create([
            'role' => UserRole::Applicant,
            'email_verified_at' => now(),
        ]);
        $profile = new ApplicantProfile(['slug' => 'ap-'.rand()]);
        $profile->user_id = $user->id;
        $profile->save();

        $doc = new Document([
            'type' => 'cv', 'original_name' => 'cv.pdf',
            'path' => 'cv/1/uuid.pdf', 'mime_type' => 'application/pdf',
            'size_bytes' => 500000, 'sha256' => str_repeat('a', 64),
        ]);
        $doc->user_id = $user->id;
        $doc->save();

        $profile->current_cv_document_id = $doc->id;
        $profile->save();
        Model::reguard();

        return [$user, $profile, $doc];
    }

    private function makeApprovedJobPosting(): array
    {
        Model::unguard();
        $companyUser = User::factory()->company()->create(['email_verified_at' => now()]);
        $company = new CompanyProfile(['slug' => 'co-'.rand(), 'company_name' => 'PT Test '.rand()]);
        $company->user_id = $companyUser->id;
        $company->verification_status = CompanyVerificationStatus::Approved;
        $company->verified_at = now();
        $company->save();

        $job = new JobPosting([
            'company_profile_id' => $company->id,
            'title' => 'Test Job '.rand(),
            'slug' => 'test-job-'.rand(),
            'description' => 'desc',
            'requirements' => 'req',
            'location' => 'Jakarta',
            'job_type' => 'full_time',
            'work_mode' => 'onsite',
        ]);
        $job->status = JobStatus::Open;
        $job->published_at = now();
        $job->save();
        Model::reguard();

        return [$companyUser, $company, $job];
    }

    /** Apply sukses: history terbentuk, notifikasi dikirim */
    public function test_applicant_can_apply_to_open_job(): void
    {
        Notification::fake();
        [$applicant, $profile, $doc] = $this->makeApplicantWithCv();
        [, , $job] = $this->makeApprovedJobPosting();

        $this->actingAs($applicant)
            ->post(route('applicant.applications.store', $job->slug), [
                'cover_letter' => 'Saya tertarik dengan posisi ini.',
            ])->assertRedirect();

        $app = Application::where('job_posting_id', $job->id)
            ->where('applicant_profile_id', $profile->id)
            ->first();

        $this->assertNotNull($app);
        $this->assertEquals(ApplicationStatus::Applied, $app->status);

        // History awal terbentuk
        $this->assertDatabaseHas('application_status_histories', [
            'application_id' => $app->id,
            'from_status' => null,
            'to_status' => 'applied',
        ]);

        Notification::assertSentTo($applicant, ApplicationReceived::class);
    }

    /** Duplikat lamaran ditolak */
    public function test_duplicate_application_is_rejected(): void
    {
        [$applicant, $profile, $doc] = $this->makeApplicantWithCv();
        [, , $job] = $this->makeApprovedJobPosting();

        $this->actingAs($applicant)->post(route('applicant.applications.store', $job->slug));

        $this->actingAs($applicant)
            ->post(route('applicant.applications.store', $job->slug))
            ->assertSessionHasErrors('apply');
    }

    /** Tanpa CV aktif ditolak */
    public function test_apply_without_cv_is_rejected(): void
    {
        Model::unguard();
        $user = User::factory()->create(['role' => UserRole::Applicant, 'email_verified_at' => now()]);
        $profile = new ApplicantProfile(['slug' => 'ap-'.rand()]);
        $profile->user_id = $user->id;
        $profile->save();
        Model::reguard();
        [, , $job] = $this->makeApprovedJobPosting();

        $this->actingAs($user)
            ->post(route('applicant.applications.store', $job->slug))
            ->assertSessionHasErrors('apply');
    }

    /** Lowongan closed ditolak */
    public function test_apply_to_closed_job_is_rejected(): void
    {
        [$applicant] = $this->makeApplicantWithCv();
        Model::unguard();
        $companyUser = User::factory()->company()->create(['email_verified_at' => now()]);
        $company = new CompanyProfile(['slug' => 'co-'.rand(), 'company_name' => 'PT X']);
        $company->user_id = $companyUser->id;
        $company->verification_status = CompanyVerificationStatus::Approved;
        $company->save();
        $closedJob = new JobPosting([
            'company_profile_id' => $company->id,
            'title' => 'Closed', 'slug' => 'closed-'.rand(),
            'description' => 'd', 'requirements' => 'r',
            'location' => 'Jakarta', 'job_type' => 'full_time', 'work_mode' => 'onsite',
        ]);
        $closedJob->status = JobStatus::Closed;
        $closedJob->save();
        Model::reguard();

        $this->actingAs($applicant)
            ->post(route('applicant.applications.store', $closedJob->slug))
            ->assertSessionHasErrors('apply');
    }

    /** CV milik orang lain ditolak */
    public function test_cv_from_other_user_is_rejected(): void
    {
        [$applicant] = $this->makeApplicantWithCv();
        [$other, , $otherDoc] = $this->makeApplicantWithCv();
        [, , $job] = $this->makeApprovedJobPosting();

        $this->actingAs($applicant)
            ->post(route('applicant.applications.store', $job->slug), [
                'cv_document_id' => $otherDoc->id,
            ])->assertSessionHasErrors('cv_document_id');
    }

    /** Withdraw valid */
    public function test_applicant_can_withdraw_application(): void
    {
        Notification::fake();
        [$applicant, $profile, $doc] = $this->makeApplicantWithCv();
        [, , $job] = $this->makeApprovedJobPosting();

        $this->actingAs($applicant)->post(route('applicant.applications.store', $job->slug));
        $app = Application::where('applicant_profile_id', $profile->id)->first();

        $this->actingAs($applicant)
            ->post(route('applicant.applications.withdraw', $app))
            ->assertRedirect();

        $this->assertEquals(ApplicationStatus::Withdrawn, $app->fresh()->status);
    }

    /** IDOR: pelamar lain tidak bisa lihat detail lamaran */
    public function test_idor_application_detail_returns_403(): void
    {
        [$owner, $profile, $doc] = $this->makeApplicantWithCv();
        [$other] = $this->makeApplicantWithCv();
        [, , $job] = $this->makeApprovedJobPosting();

        $this->actingAs($owner)->post(route('applicant.applications.store', $job->slug));
        $app = Application::where('applicant_profile_id', $profile->id)->first();

        $this->actingAs($other)
            ->withoutVite()
            ->get(route('applicant.applications.show', $app))
            ->assertForbidden();
    }

    /** Withdraw tidak bisa untuk status final */
    public function test_cannot_withdraw_final_application(): void
    {
        [$applicant, $profile, $doc] = $this->makeApplicantWithCv();
        [, , $job] = $this->makeApprovedJobPosting();

        $this->actingAs($applicant)->post(route('applicant.applications.store', $job->slug));
        $app = Application::where('applicant_profile_id', $profile->id)->first();

        // Set ke accepted (final)
        Model::unguard();
        $app->status = ApplicationStatus::Accepted;
        $app->save();
        Model::reguard();

        $this->actingAs($applicant)
            ->post(route('applicant.applications.withdraw', $app))
            ->assertForbidden();
    }

    /** ApplicationStatusService menolak transisi tidak valid */
    public function test_service_rejects_invalid_transition(): void
    {
        [$applicant, $profile, $doc] = $this->makeApplicantWithCv();
        [, , $job] = $this->makeApprovedJobPosting();

        $this->actingAs($applicant)->post(route('applicant.applications.store', $job->slug));
        $app = Application::where('applicant_profile_id', $profile->id)->first();

        // Set ke accepted
        Model::unguard();
        $app->status = ApplicationStatus::Accepted;
        $app->save();
        Model::reguard();

        $service = new ApplicationStatusService;

        $this->expectException(\DomainException::class);
        $service->transition($app, ApplicationStatus::Applied, $applicant);
    }
}
