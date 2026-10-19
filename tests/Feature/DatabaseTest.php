<?php

namespace Tests\Feature;

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
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DatabaseTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Helper: buat job + applicant + document via factory & forceCreate.
     * Memakai forceCreate/unguard karena user_id adalah FK, bukan fillable.
     */
    private function makeJobApplicantDoc(): array
    {
        Model::unguard();

        $companyUser = User::factory()->company()->create();
        $company = CompanyProfile::create([
            'user_id' => $companyUser->id,
            'slug' => 'co-'.rand(),
            'company_name' => 'Test Corp',
        ]);
        $company->verification_status = CompanyVerificationStatus::Approved;
        $company->save();

        $job = new JobPosting([
            'company_profile_id' => $company->id,
            'title' => 'Test Job',
            'slug' => 'job-'.rand(),
            'description' => 'desc',
            'requirements' => 'req',
            'location' => 'Jakarta',
            'job_type' => 'full_time',
            'work_mode' => 'onsite',
        ]);
        $job->status = JobStatus::Open;
        $job->published_at = now();
        $job->save();

        $applicantUser = User::factory()->create();
        $applicant = ApplicantProfile::create([
            'user_id' => $applicantUser->id,
            'slug' => 'ap-'.rand(),
        ]);

        $doc = Document::create([
            'user_id' => $applicantUser->id,
            'type' => 'cv',
            'original_name' => 'cv.pdf',
            'path' => 'cv/1/uuid.pdf',
            'mime_type' => 'application/pdf',
            'size_bytes' => 500000,
            'sha256' => str_repeat('a', 64),
        ]);

        Model::reguard();

        return [$job, $applicant, $doc];
    }

    /** Unique constraint: satu pelamar hanya bisa melamar sekali ke satu lowongan. */
    public function test_application_unique_per_job_and_applicant(): void
    {
        $this->expectException(UniqueConstraintViolationException::class);

        [$job, $applicant, $doc] = $this->makeJobApplicantDoc();

        $data = [
            'job_posting_id' => $job->id,
            'applicant_profile_id' => $applicant->id,
            'cv_document_id' => $doc->id,
        ];

        $app1 = new Application($data);
        $app1->status = ApplicationStatus::Applied;
        $app1->save();

        $app2 = new Application($data);
        $app2->status = ApplicationStatus::Applied;
        $app2->save(); // harus gagal
    }

    /** Relasi user → applicant_profile. */
    public function test_user_has_applicant_profile(): void
    {
        Model::unguard();
        $user = User::factory()->create(['role' => UserRole::Applicant]);
        $profile = ApplicantProfile::create(['user_id' => $user->id, 'slug' => 'test-'.rand()]);
        Model::reguard();

        $user->refresh();
        $this->assertInstanceOf(ApplicantProfile::class, $user->applicantProfile);
        $this->assertEquals($profile->id, $user->applicantProfile->id);
    }

    /** role tidak boleh di fillable User. */
    public function test_role_not_in_user_fillable(): void
    {
        $this->assertNotContains('role', (new User)->getFillable());
    }

    /** status tidak boleh di fillable Application. */
    public function test_status_not_in_application_fillable(): void
    {
        $this->assertNotContains('status', (new Application)->getFillable());
    }

    /** Scope open() hanya return lowongan open milik perusahaan approved. */
    public function test_scope_open_excludes_draft_and_pending_company(): void
    {
        Model::unguard();

        $approvedUser = User::factory()->company()->create();
        $approvedCompany = CompanyProfile::create(['user_id' => $approvedUser->id, 'slug' => 'ca-'.rand(), 'company_name' => 'A']);
        $approvedCompany->verification_status = CompanyVerificationStatus::Approved;
        $approvedCompany->save();

        $pendingUser = User::factory()->company()->create();
        $pendingCompany = CompanyProfile::create(['user_id' => $pendingUser->id, 'slug' => 'cb-'.rand(), 'company_name' => 'B']);
        // verification_status tetap pending (default)

        // Lowongan open milik perusahaan approved
        $openJob = new JobPosting([
            'company_profile_id' => $approvedCompany->id,
            'title' => 'Open Job', 'slug' => 'oj-'.rand(),
            'description' => 'desc', 'requirements' => 'req',
            'location' => 'Jakarta', 'job_type' => 'full_time', 'work_mode' => 'onsite',
        ]);
        $openJob->status = JobStatus::Open;
        $openJob->published_at = now();
        $openJob->save();

        // Draft milik perusahaan approved
        $draftJob = new JobPosting([
            'company_profile_id' => $approvedCompany->id,
            'title' => 'Draft Job', 'slug' => 'dj-'.rand(),
            'description' => 'desc', 'requirements' => 'req',
            'location' => 'Jakarta', 'job_type' => 'full_time', 'work_mode' => 'onsite',
        ]);
        $draftJob->status = JobStatus::Draft;
        $draftJob->save();

        // Open milik perusahaan pending
        $pendingCoJob = new JobPosting([
            'company_profile_id' => $pendingCompany->id,
            'title' => 'Pending Co Job', 'slug' => 'pj-'.rand(),
            'description' => 'desc', 'requirements' => 'req',
            'location' => 'Jakarta', 'job_type' => 'full_time', 'work_mode' => 'onsite',
        ]);
        $pendingCoJob->status = JobStatus::Open;
        $pendingCoJob->published_at = now();
        $pendingCoJob->save();

        Model::reguard();

        $openIds = JobPosting::open()->pluck('id')->toArray();

        $this->assertContains($openJob->id, $openIds, 'Lowongan open harus muncul');
        $this->assertNotContains($draftJob->id, $openIds, 'Draft tidak boleh muncul');
        $this->assertNotContains($pendingCoJob->id, $openIds, 'Perusahaan pending tidak boleh muncul');
    }
}
