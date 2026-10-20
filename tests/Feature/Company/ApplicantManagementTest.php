<?php

namespace Tests\Feature\Company;

use App\Enums\ApplicationStatus;
use App\Enums\CompanyVerificationStatus;
use App\Enums\JobStatus;
use App\Enums\UserRole;
use App\Models\ApplicantProfile;
use App\Models\Application;
use App\Models\ApplicationStatusHistory;
use App\Models\CompanyProfile;
use App\Models\Document;
use App\Models\JobPosting;
use App\Models\User;
use App\Notifications\ApplicationStatusChanged;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class ApplicantManagementTest extends TestCase
{
    use RefreshDatabase;

    private function makeApprovedCompanyWithJob(): array
    {
        Model::unguard();
        $user = User::factory()->company()->create(['email_verified_at' => now()]);
        $company = new CompanyProfile(['slug' => 'co-'.rand(), 'company_name' => 'PT Test '.rand()]);
        $company->user_id = $user->id;
        $company->verification_status = CompanyVerificationStatus::Approved;
        $company->verified_at = now();
        $company->save();

        $job = new JobPosting([
            'company_profile_id' => $company->id,
            'title' => 'Job '.rand(), 'slug' => 'job-'.rand(),
            'description' => 'd', 'requirements' => 'r',
            'location' => 'Jakarta', 'job_type' => 'full_time', 'work_mode' => 'onsite',
        ]);
        $job->status = JobStatus::Open;
        $job->published_at = now();
        $job->save();
        Model::reguard();

        return [$user, $company, $job];
    }

    private function makeApplicantWithApplication(JobPosting $job): array
    {
        Model::unguard();
        $user = User::factory()->create(['role' => UserRole::Applicant, 'email_verified_at' => now()]);
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

        $app = new Application([
            'job_posting_id' => $job->id,
            'applicant_profile_id' => $profile->id,
            'cv_document_id' => $doc->id,
        ]);
        $app->status = ApplicationStatus::Applied;
        $app->save();

        // History awal
        $h = new ApplicationStatusHistory([
            'application_id' => $app->id,
            'from_status' => null, 'to_status' => 'applied',
            'created_at' => now(),
        ]);
        $h->save();

        Model::reguard();

        return [$user, $profile, $doc, $app];
    }

    /** Perusahaan lain tidak bisa melihat pelamar */
    public function test_other_company_cannot_view_applicants(): void
    {
        [$owner, , $job] = $this->makeApprovedCompanyWithJob();
        [$other] = $this->makeApprovedCompanyWithJob();

        $this->actingAs($other)
            ->withoutVite()
            ->get(route('company.applicants.index', $job))
            ->assertForbidden();
    }

    /** Auto-viewed saat perusahaan pertama buka detail */
    public function test_auto_viewed_on_first_open(): void
    {
        [$companyUser, , $job] = $this->makeApprovedCompanyWithJob();
        [, , , $app] = $this->makeApplicantWithApplication($job);

        $this->assertEquals(ApplicationStatus::Applied, $app->status);

        $this->actingAs($companyUser)
            ->withoutVite()
            ->get(route('company.applicants.show', $app));

        $this->assertEquals(ApplicationStatus::Viewed, $app->fresh()->status);

        // Idempoten: buka lagi tidak mengubah status
        $this->actingAs($companyUser)
            ->withoutVite()
            ->get(route('company.applicants.show', $app));

        $this->assertDatabaseCount('application_status_histories', 2); // applied + viewed
    }

    /** Keputusan valid: accept */
    public function test_company_can_accept_applicant(): void
    {
        Notification::fake();
        [$companyUser, , $job] = $this->makeApprovedCompanyWithJob();
        [, , , $app] = $this->makeApplicantWithApplication($job);

        // Set ke viewed dulu
        Model::unguard();
        $app->status = ApplicationStatus::Viewed;
        $app->save();
        Model::reguard();

        $this->actingAs($companyUser)
            ->post(route('company.applicants.decide', $app), [
                'decision' => 'accepted',
                'note' => 'Selamat!',
            ])->assertRedirect();

        $this->assertEquals(ApplicationStatus::Accepted, $app->fresh()->status);
        Notification::assertSentTo(
            $app->applicantProfile->user,
            ApplicationStatusChanged::class
        );
    }

    /** company_note tidak bocor ke pelamar */
    public function test_company_note_not_visible_to_applicant(): void
    {
        [$companyUser, , $job] = $this->makeApprovedCompanyWithJob();
        [$applicantUser, , , $app] = $this->makeApplicantWithApplication($job);

        // Simpan catatan privat
        $this->actingAs($companyUser)
            ->post(route('company.applicants.note', $app), [
                'company_note' => 'RAHASIA: pelamar ini kurang bagus',
            ]);

        // Pelamar tidak boleh lihat company_note di detail lamarannya
        $response = $this->actingAs($applicantUser)
            ->withoutVite()
            ->get(route('applicant.applications.show', $app));

        $response->assertOk()->assertDontSee('RAHASIA: pelamar ini kurang bagus');
    }

    /** Perusahaan lain tidak bisa memutuskan */
    public function test_other_company_cannot_decide(): void
    {
        [, , $job] = $this->makeApprovedCompanyWithJob();
        [$other] = $this->makeApprovedCompanyWithJob();
        [, , , $app] = $this->makeApplicantWithApplication($job);

        $this->actingAs($other)
            ->post(route('company.applicants.decide', $app), ['decision' => 'accepted'])
            ->assertForbidden();
    }

    /** Admin tidak bisa akses panel perusahaan (middleware role:company) — ini desain yang benar */
    public function test_admin_cannot_access_company_panel(): void
    {
        Model::unguard();
        $admin = User::factory()->admin()->create(['email_verified_at' => now()]);
        Model::reguard();
        [, , $job] = $this->makeApprovedCompanyWithJob();
        [, , , $app] = $this->makeApplicantWithApplication($job);

        // Admin tidak punya akses ke panel perusahaan (role:company middleware)
        $this->actingAs($admin)
            ->get(route('company.applicants.show', $app))
            ->assertForbidden();

        // Admin juga tidak bisa memutuskan
        $this->actingAs($admin)
            ->post(route('company.applicants.decide', $app), ['decision' => 'accepted'])
            ->assertForbidden();
    }
}
