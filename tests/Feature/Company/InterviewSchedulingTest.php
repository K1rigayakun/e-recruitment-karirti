<?php

namespace Tests\Feature\Company;

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
use App\Notifications\InterviewScheduled;
use App\Services\ApplicationStatusService;
use App\Services\InterviewScheduler;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class InterviewSchedulingTest extends TestCase
{
    use RefreshDatabase;

    private function makeSetup(): array
    {
        Model::unguard();
        // Perusahaan
        $companyUser = User::factory()->company()->create(['email_verified_at' => now()]);
        $company = new CompanyProfile(['slug' => 'co-'.rand(), 'company_name' => 'PT Test']);
        $company->user_id = $companyUser->id;
        $company->verification_status = CompanyVerificationStatus::Approved;
        $company->verified_at = now();
        $company->save();

        $job = new JobPosting([
            'company_profile_id' => $company->id,
            'title' => 'Dev', 'slug' => 'dev-'.rand(),
            'description' => 'd', 'requirements' => 'r',
            'location' => 'Jakarta', 'job_type' => 'full_time', 'work_mode' => 'onsite',
        ]);
        $job->status = JobStatus::Open;
        $job->published_at = now();
        $job->save();

        // Pelamar + lamaran
        $applicantUser = User::factory()->create(['role' => UserRole::Applicant, 'email_verified_at' => now()]);
        $profile = new ApplicantProfile(['slug' => 'ap-'.rand()]);
        $profile->user_id = $applicantUser->id;
        $profile->save();

        $doc = new Document([
            'type' => 'cv', 'original_name' => 'cv.pdf',
            'path' => 'cv/1/uuid.pdf', 'mime_type' => 'application/pdf',
            'size_bytes' => 500000, 'sha256' => str_repeat('a', 64),
        ]);
        $doc->user_id = $applicantUser->id;
        $doc->save();

        $app = new Application([
            'job_posting_id' => $job->id,
            'applicant_profile_id' => $profile->id,
            'cv_document_id' => $doc->id,
        ]);
        $app->status = ApplicationStatus::Viewed;
        $app->save();

        Model::reguard();

        return [$companyUser, $applicantUser, $app];
    }

    /** Jadwal berhasil: status lamaran jadi interview */
    public function test_schedule_changes_application_to_interview(): void
    {
        Notification::fake();
        [$companyUser, , $app] = $this->makeSetup();

        $this->actingAs($companyUser)
            ->post(route('company.interviews.store', $app), [
                'scheduled_at' => now()->addHours(2)->format('Y-m-d\TH:i'),
                'duration_minutes' => 60,
                'mode' => 'online',
                'meeting_url' => 'https://meet.google.com/test',
            ])->assertRedirect();

        $this->assertEquals(ApplicationStatus::Interview, $app->fresh()->status);
        Notification::assertSentTo($app->applicantProfile->user, InterviewScheduled::class);
    }

    /** Waktu masa lalu ditolak */
    public function test_past_scheduled_at_rejected(): void
    {
        [$companyUser, , $app] = $this->makeSetup();

        $this->actingAs($companyUser)
            ->post(route('company.interviews.store', $app), [
                'scheduled_at' => now()->subHour()->format('Y-m-d\TH:i'),
                'duration_minutes' => 60,
                'mode' => 'online',
                'meeting_url' => 'https://meet.google.com/test',
            ])->assertSessionHasErrors('scheduled_at');
    }

    /** Online tanpa URL ditolak */
    public function test_online_without_url_rejected(): void
    {
        [$companyUser, , $app] = $this->makeSetup();

        $this->actingAs($companyUser)
            ->post(route('company.interviews.store', $app), [
                'scheduled_at' => now()->addHours(2)->format('Y-m-d\TH:i'),
                'duration_minutes' => 60,
                'mode' => 'online',
                // meeting_url kosong
            ])->assertSessionHasErrors('meeting_url');
    }

    /** Onsite tanpa lokasi ditolak */
    public function test_onsite_without_location_rejected(): void
    {
        [$companyUser, , $app] = $this->makeSetup();

        $this->actingAs($companyUser)
            ->post(route('company.interviews.store', $app), [
                'scheduled_at' => now()->addHours(2)->format('Y-m-d\TH:i'),
                'duration_minutes' => 60,
                'mode' => 'onsite',
                // location kosong
            ])->assertSessionHasErrors('location');
    }

    /** URL http (bukan https) ditolak */
    public function test_http_meeting_url_rejected(): void
    {
        [$companyUser, , $app] = $this->makeSetup();

        $this->actingAs($companyUser)
            ->post(route('company.interviews.store', $app), [
                'scheduled_at' => now()->addHours(2)->format('Y-m-d\TH:i'),
                'duration_minutes' => 60,
                'mode' => 'online',
                'meeting_url' => 'http://meet.google.com/test', // http bukan https
            ])->assertSessionHasErrors('meeting_url');
    }

    /** Hanya perusahaan pemilik yang bisa jadwalkan */
    public function test_other_company_cannot_schedule(): void
    {
        [, , $app] = $this->makeSetup();
        [$otherCompany] = $this->makeSetup(); // perusahaan lain

        $this->actingAs($otherCompany)
            ->post(route('company.interviews.store', $app), [
                'scheduled_at' => now()->addHours(2)->format('Y-m-d\TH:i'),
                'duration_minutes' => 60,
                'mode' => 'online',
                'meeting_url' => 'https://meet.google.com/test',
            ])->assertForbidden();
    }

    /** Pelamar bisa konfirmasi hadir */
    public function test_applicant_can_confirm_interview(): void
    {
        Notification::fake();
        [$companyUser, $applicantUser, $app] = $this->makeSetup();

        // Jadwalkan dulu
        $scheduler = new InterviewScheduler(new ApplicationStatusService);
        Model::unguard();
        $interview = $scheduler->schedule($app, $companyUser, [
            'scheduled_at' => now()->addHours(3),
            'duration_minutes' => 60,
            'mode' => 'online',
            'meeting_url' => 'https://meet.google.com/test',
        ]);
        Model::reguard();

        $this->actingAs($applicantUser)
            ->post(route('applicant.interviews.confirm', $interview))
            ->assertRedirect();

        $this->assertEquals('confirmed', $interview->fresh()->status->value);
    }

    /** Lamaran final tidak bisa dijadwalkan */
    public function test_final_application_cannot_be_scheduled(): void
    {
        [$companyUser, , $app] = $this->makeSetup();

        Model::unguard();
        $app->status = ApplicationStatus::Rejected;
        $app->save();
        Model::reguard();

        $this->actingAs($companyUser)
            ->post(route('company.interviews.store', $app), [
                'scheduled_at' => now()->addHours(2)->format('Y-m-d\TH:i'),
                'duration_minutes' => 60,
                'mode' => 'online',
                'meeting_url' => 'https://meet.google.com/test',
            ])->assertForbidden();
    }
}
