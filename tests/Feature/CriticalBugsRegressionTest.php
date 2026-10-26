<?php

namespace Tests\Feature;

use App\Enums\ApplicationStatus;
use App\Enums\CompanyVerificationStatus;
use App\Enums\InterviewMode;
use App\Enums\JobStatus;
use App\Enums\JobType;
use App\Enums\UserRole;
use App\Enums\WorkMode;
use App\Models\ApplicantProfile;
use App\Models\Application;
use App\Models\CompanyProfile;
use App\Models\Document;
use App\Models\JobPosting;
use App\Models\User;
use App\Notifications\ApplicationReceived;
use App\Services\InterviewScheduler;
use App\Services\ReportBuilder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class CriticalBugsRegressionTest extends TestCase
{
    use RefreshDatabase;

    /** A1: ReportBuilder monthlyTrend query must work without throwing strftime error */
    public function test_a1_report_builder_monthly_trend_executes_successfully(): void
    {
        $reportBuilder = app(ReportBuilder::class);
        $trend = $reportBuilder->monthlyTrend();

        $this->assertIsArray($trend);
    }

    /** A2: Production CSP header contains non-empty nonce */
    public function test_a2_production_csp_has_valid_nonce(): void
    {
        $this->app['env'] = 'production';

        $response = $this->get('/');
        $response->assertOk();

        $csp = $response->headers->get('Content-Security-Policy');
        $this->assertNotNull($csp);
        $this->assertMatchesRegularExpression("/'nonce-[a-zA-Z0-9+\/=]{16,}'/", $csp);
    }

    /** A3: Interview scheduled_at does not drift hours */
    public function test_a3_interview_time_does_not_drift(): void
    {
        Carbon::setTestNow('2026-10-25 10:00:00');

        $companyUser = User::factory()->create(['role' => UserRole::Company]);
        $companyProfile = CompanyProfile::factory()->create([
            'user_id' => $companyUser->id,
            'verification_status' => CompanyVerificationStatus::Approved,
        ]);

        $job = JobPosting::factory()->create([
            'company_profile_id' => $companyProfile->id,
            'status' => JobStatus::Open,
        ]);

        $applicantUser = User::factory()->create(['role' => UserRole::Applicant]);
        $applicantProfile = ApplicantProfile::factory()->create(['user_id' => $applicantUser->id]);
        $document = Document::factory()->create(['user_id' => $applicantUser->id]);

        $application = Application::forceCreate([
            'job_posting_id' => $job->id,
            'applicant_profile_id' => $applicantProfile->id,
            'cv_document_id' => $document->id,
            'status' => ApplicationStatus::Applied,
        ]);

        $scheduler = app(InterviewScheduler::class);
        $scheduledTime = Carbon::parse('2026-10-25 14:00:00');

        $interview = $scheduler->schedule($application, $companyUser, [
            'scheduled_at' => $scheduledTime,
            'duration_minutes' => 60,
            'mode' => InterviewMode::Online,
            'meeting_url' => 'https://meet.google.com/abc-defg-hij',
        ]);

        $this->assertEquals('2026-10-25 14:00:00', $interview->scheduled_at->format('Y-m-d H:i:s'));
    }

    /** A4b: ApplicationReceived notification to company has company link and text */
    public function test_a4b_application_received_notification_company_format(): void
    {
        $companyUser = User::factory()->create(['role' => UserRole::Company]);
        $companyProfile = CompanyProfile::factory()->create([
            'user_id' => $companyUser->id,
            'company_name' => 'PT KarirTI Solusi',
        ]);

        $job = JobPosting::factory()->create([
            'company_profile_id' => $companyProfile->id,
            'title' => 'Backend Engineer',
        ]);

        $applicantUser = User::factory()->create(['name' => 'Budi Santoso', 'role' => UserRole::Applicant]);
        $applicantProfile = ApplicantProfile::factory()->create(['user_id' => $applicantUser->id]);
        $document = Document::factory()->create(['user_id' => $applicantUser->id]);

        $application = Application::forceCreate([
            'job_posting_id' => $job->id,
            'applicant_profile_id' => $applicantProfile->id,
            'cv_document_id' => $document->id,
            'status' => ApplicationStatus::Applied,
        ]);

        $notification = new ApplicationReceived($application);
        $mail = $notification->toMail($companyUser);

        $this->assertStringContainsString('Lamaran Baru', $mail->subject);
        $this->assertStringContainsString(route('company.applicants.show', [$job->id, $application->id]), $mail->actionUrl);
    }

    /** A5: Flash message 'toast' is rendered in HTML */
    public function test_a5_toast_region_renders_toast_session_key(): void
    {
        $response = $this->withSession(['toast' => 'Profil berhasil disimpan!'])->get('/');
        $response->assertSee('Profil berhasil disimpan!');
        $response->assertSee('data-toast-tone="mint"', false);
    }

    /** B2: Admin user search does not leak other roles */
    public function test_b2_admin_user_search_preserves_role_filter(): void
    {
        $admin = User::factory()->create(['role' => UserRole::Admin, 'name' => 'Admin Utama']);
        User::factory()->create(['role' => UserRole::Company, 'name' => 'HRD Perusahaan']);
        User::factory()->create(['role' => UserRole::Applicant, 'name' => 'HRD Pelamar']);

        $response = $this->actingAs($admin)->get(route('admin.users.index', [
            'q' => 'HRD',
            'role' => 'admin',
        ]));

        $response->assertOk();
        $users = $response->viewData('users');
        $this->assertCount(0, $users);
    }

    /** B3: Applicant sorting by profile keeps application id intact */
    public function test_b3_company_applicant_sort_profile_preserves_application_id(): void
    {
        $companyUser = User::factory()->create(['role' => UserRole::Company]);
        $companyProfile = CompanyProfile::factory()->create([
            'user_id' => $companyUser->id,
            'verification_status' => CompanyVerificationStatus::Approved,
        ]);

        $job = JobPosting::factory()->create(['company_profile_id' => $companyProfile->id]);

        $applicantUser = User::factory()->create(['role' => UserRole::Applicant]);
        $applicantProfile = ApplicantProfile::factory()->create([
            'user_id' => $applicantUser->id,
            'alumni_verified_at' => now(),
        ]);
        $document = Document::factory()->create(['user_id' => $applicantUser->id]);

        $application = Application::forceCreate([
            'job_posting_id' => $job->id,
            'applicant_profile_id' => $applicantProfile->id,
            'cv_document_id' => $document->id,
            'status' => ApplicationStatus::Applied,
        ]);

        $response = $this->actingAs($companyUser)->get(route('company.applicants.index', [
            'job' => $job->id,
            'sort' => 'profile',
        ]));

        $response->assertOk();
        $apps = $response->viewData('applications');
        $this->assertEquals($application->id, $apps->first()->id);
    }

    /** B4: Job show_salary can be updated to false */
    public function test_b4_company_can_uncheck_show_salary(): void
    {
        $companyUser = User::factory()->create(['role' => UserRole::Company]);
        $companyProfile = CompanyProfile::factory()->create([
            'user_id' => $companyUser->id,
            'verification_status' => CompanyVerificationStatus::Approved,
        ]);

        $job = JobPosting::factory()->create([
            'company_profile_id' => $companyProfile->id,
            'show_salary' => true,
        ]);

        $response = $this->actingAs($companyUser)->put(route('company.jobs.update', $job), [
            'title' => 'Lowongan Update',
            'summary' => 'Ringkasan',
            'description' => 'Deskripsi pekerjaan',
            'requirements' => 'Persyaratan pekerjaan',
            'location' => 'Medan',
            'job_type' => JobType::FullTime->value,
            'work_mode' => WorkMode::Onsite->value,
            'show_salary' => '0',
            'skills' => [],
        ]);

        $response->assertRedirect(route('company.jobs.index'));
        $job->refresh();
        $this->assertFalse($job->show_salary);
    }

    /** B8: Company renaming resets verification status to pending */
    public function test_b8_company_identity_change_resets_verification(): void
    {
        $companyUser = User::factory()->create(['role' => UserRole::Company]);
        $companyProfile = CompanyProfile::factory()->create([
            'user_id' => $companyUser->id,
            'company_name' => 'PT Nama Lama',
            'verification_status' => CompanyVerificationStatus::Approved,
        ]);

        $response = $this->actingAs($companyUser)->put(route('company.profile.update'), [
            'company_name' => 'PT Nama Baru Berbeda',
            'industry' => 'Teknologi',
            'address' => 'Jl. Baru No. 1',
        ]);

        $response->assertRedirect();
        $companyProfile->refresh();
        $this->assertEquals(CompanyVerificationStatus::Pending, $companyProfile->verification_status);
        $this->assertNull($companyProfile->verified_at);
    }
}
