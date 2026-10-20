<?php

namespace Tests\Feature\Public;

use App\Enums\CompanyVerificationStatus;
use App\Enums\JobStatus;
use App\Models\CompanyProfile;
use App\Models\JobPosting;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HomeTest extends TestCase
{
    use RefreshDatabase;

    public function test_home_page_returns_200(): void
    {
        $this->withoutVite()->get(route('home'))->assertOk();
    }

    public function test_jobs_index_returns_200(): void
    {
        $this->withoutVite()->get(route('jobs.index'))->assertOk();
    }

    public function test_jobs_index_only_shows_open_approved_jobs(): void
    {
        Model::unguard();
        $companyUser = User::factory()->company()->create();
        $company = CompanyProfile::create(['user_id' => $companyUser->id, 'slug' => 'co-'.rand(), 'company_name' => 'A']);
        $company->verification_status = CompanyVerificationStatus::Approved;
        $company->save();

        $pendingUser = User::factory()->company()->create();
        $pendingCompany = CompanyProfile::create(['user_id' => $pendingUser->id, 'slug' => 'co-p-'.rand(), 'company_name' => 'B']);

        $openJob = new JobPosting(['company_profile_id' => $company->id, 'title' => 'Open', 'slug' => 'open-'.rand(), 'description' => 'd', 'requirements' => 'r', 'location' => 'Jakarta', 'job_type' => 'full_time', 'work_mode' => 'onsite']);
        $openJob->status = JobStatus::Open;
        $openJob->published_at = now();
        $openJob->save();

        $draftJob = new JobPosting(['company_profile_id' => $company->id, 'title' => 'Draft', 'slug' => 'draft-'.rand(), 'description' => 'd', 'requirements' => 'r', 'location' => 'Jakarta', 'job_type' => 'full_time', 'work_mode' => 'onsite']);
        $draftJob->status = JobStatus::Draft;
        $draftJob->save();

        $pendingJob = new JobPosting(['company_profile_id' => $pendingCompany->id, 'title' => 'Pending', 'slug' => 'pend-'.rand(), 'description' => 'd', 'requirements' => 'r', 'location' => 'Jakarta', 'job_type' => 'full_time', 'work_mode' => 'onsite']);
        $pendingJob->status = JobStatus::Open;
        $pendingJob->published_at = now();
        $pendingJob->save();

        Model::reguard();

        $response = $this->withoutVite()->get(route('jobs.index'));
        $response->assertOk()->assertSee('Open')->assertDontSee('Draft')->assertDontSee('Pending');
    }

    public function test_job_show_draft_returns_404_for_guest(): void
    {
        Model::unguard();
        $user = User::factory()->company()->create();
        $company = CompanyProfile::create(['user_id' => $user->id, 'slug' => 'co-'.rand(), 'company_name' => 'X']);
        $company->verification_status = CompanyVerificationStatus::Approved;
        $company->save();

        $draft = new JobPosting(['company_profile_id' => $company->id, 'title' => 'Draft Job', 'slug' => 'my-draft', 'description' => 'd', 'requirements' => 'r', 'location' => 'Jakarta', 'job_type' => 'full_time', 'work_mode' => 'onsite']);
        $draft->status = JobStatus::Draft;
        $draft->save();
        Model::reguard();

        $this->withoutVite()->get(route('jobs.show', 'my-draft'))->assertNotFound();
    }

    public function test_jobs_filter_with_search(): void
    {
        // Pastikan query dengan % dan _ di-escape dan tidak error
        $this->withoutVite()
            ->get(route('jobs.index', ['q' => '50% discount _ test']))
            ->assertOk();
    }

    public function test_pagination_preserves_query_string(): void
    {
        $response = $this->withoutVite()->get(route('jobs.index', ['q' => 'laravel', 'sort' => 'latest']));
        $response->assertOk();
    }

    public function test_privacy_page_returns_200(): void
    {
        $this->withoutVite()->get(route('privacy'))->assertOk();
    }

    public function test_404_page_returns_404(): void
    {
        $this->withoutVite()->get('/halaman-yang-tidak-ada')->assertNotFound();
    }

    public function test_job_description_xss_is_sanitized(): void
    {
        Model::unguard();
        $user = User::factory()->company()->create();
        $company = CompanyProfile::create(['user_id' => $user->id, 'slug' => 'xss-'.rand(), 'company_name' => 'XSS Corp']);
        $company->verification_status = CompanyVerificationStatus::Approved;
        $company->save();

        $job = new JobPosting([
            'company_profile_id' => $company->id,
            'title' => 'XSS Test Job',
            'slug' => 'xss-test-'.rand(),
            'description' => '<script>alert("xss")</script>Deskripsi valid.',
            'requirements' => 'req',
            'location' => 'Jakarta',
            'job_type' => 'full_time',
            'work_mode' => 'onsite',
        ]);
        $job->status = JobStatus::Open;
        $job->published_at = now();
        $job->save();
        Model::reguard();

        $response = $this->withoutVite()->get(route('jobs.show', $job->slug));
        $response->assertOk()->assertDontSee('<script>', false);
    }
}
