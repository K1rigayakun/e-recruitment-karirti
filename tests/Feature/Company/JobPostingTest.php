<?php

namespace Tests\Feature\Company;

use App\Enums\CompanyVerificationStatus;
use App\Enums\JobStatus;
use App\Models\CompanyProfile;
use App\Models\JobPosting;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class JobPostingTest extends TestCase
{
    use RefreshDatabase;

    private function makeCompany(string $verificationStatus = 'approved'): array
    {
        Model::unguard();
        $user = User::factory()->company()->create(['email_verified_at' => now()]);
        $profile = new CompanyProfile([
            'slug' => 'co-'.rand(),
            'company_name' => 'PT Test '.rand(),
        ]);
        $profile->user_id = $user->id;
        $profile->verification_status = CompanyVerificationStatus::from($verificationStatus);
        if ($verificationStatus === 'approved') {
            $profile->verified_at = now();
        }
        $profile->save();
        Model::reguard();

        return [$user, $profile];
    }

    private function makeJobFor(CompanyProfile $profile, string $status = 'draft'): JobPosting
    {
        Model::unguard();
        $job = new JobPosting([
            'company_profile_id' => $profile->id,
            'title' => 'Job '.rand(),
            'slug' => 'job-'.rand(),
            'description' => 'deskripsi',
            'requirements' => 'syarat',
            'location' => 'Jakarta',
            'job_type' => 'full_time',
            'work_mode' => 'onsite',
        ]);
        $job->status = JobStatus::from($status);
        $job->save();
        Model::reguard();

        return $job;
    }

    public function test_approved_company_can_publish_job(): void
    {
        [$user, $profile] = $this->makeCompany('approved');
        $job = $this->makeJobFor($profile, 'draft');

        $this->actingAs($user)
            ->post(route('company.jobs.publish', $job))
            ->assertRedirect();

        $this->assertEquals(JobStatus::Open, $job->fresh()->status);
    }

    public function test_pending_company_cannot_publish(): void
    {
        [$user, $profile] = $this->makeCompany('pending');
        $job = $this->makeJobFor($profile, 'draft');

        $this->actingAs($user)
            ->post(route('company.jobs.publish', $job))
            ->assertForbidden();
    }

    public function test_other_company_cannot_edit_job(): void
    {
        [$owner, $profile] = $this->makeCompany('approved');
        [$other] = $this->makeCompany('approved');
        $job = $this->makeJobFor($profile, 'draft');

        $this->actingAs($other)
            ->put(route('company.jobs.update', $job), [
                'title' => 'Hack',
                'description' => 'x',
                'requirements' => 'x',
                'location' => 'x',
                'job_type' => 'full_time',
                'work_mode' => 'onsite',
            ])->assertForbidden();
    }

    public function test_only_draft_without_applications_can_be_deleted(): void
    {
        [$user, $profile] = $this->makeCompany('approved');

        // Draft tanpa lamaran — boleh hapus
        $draft = $this->makeJobFor($profile, 'draft');
        $this->actingAs($user)
            ->delete(route('company.jobs.destroy', $draft))
            ->assertRedirect();

        $this->assertModelMissing($draft);
    }

    public function test_open_job_cannot_be_deleted(): void
    {
        [$user, $profile] = $this->makeCompany('approved');
        $job = $this->makeJobFor($profile, 'open');

        $this->actingAs($user)
            ->delete(route('company.jobs.destroy', $job))
            ->assertForbidden();
    }

    public function test_slug_is_unique(): void
    {
        [$user, $profile] = $this->makeCompany('approved');

        // Buat dua lowongan dengan judul sama
        $data = [
            'title' => 'Laravel Developer',
            'description' => 'deskripsi',
            'requirements' => 'syarat',
            'location' => 'Jakarta',
            'job_type' => 'full_time',
            'work_mode' => 'onsite',
        ];

        $this->actingAs($user)->post(route('company.jobs.store'), $data);
        $this->actingAs($user)->post(route('company.jobs.store'), $data);

        $slugs = JobPosting::where('company_profile_id', $profile->id)->pluck('slug');
        $this->assertEquals($slugs->count(), $slugs->unique()->count());
    }

    public function test_salary_max_must_be_gte_min(): void
    {
        [$user] = $this->makeCompany('approved');

        $this->actingAs($user)
            ->post(route('company.jobs.store'), [
                'title' => 'Test',
                'description' => 'd',
                'requirements' => 'r',
                'location' => 'Jakarta',
                'job_type' => 'full_time',
                'work_mode' => 'onsite',
                'salary_min' => 5000000,
                'salary_max' => 3000000, // lebih kecil dari min
            ])->assertSessionHasErrors('salary_max');
    }

    public function test_deadline_cannot_be_in_the_past(): void
    {
        [$user] = $this->makeCompany('approved');

        $this->actingAs($user)
            ->post(route('company.jobs.store'), [
                'title' => 'Test',
                'description' => 'd',
                'requirements' => 'r',
                'location' => 'Jakarta',
                'job_type' => 'full_time',
                'work_mode' => 'onsite',
                'deadline_at' => now()->subDay()->format('Y-m-d'),
            ])->assertSessionHasErrors('deadline_at');
    }

    public function test_xss_in_description_is_sanitized(): void
    {
        [$user, $profile] = $this->makeCompany('approved');
        $job = $this->makeJobFor($profile, 'open');

        // Deskripsi dengan XSS — safe-markdown harus strip tag script
        Model::unguard();
        $job->description = '<script>alert("xss")</script>Deskripsi valid.';
        $job->save();
        Model::reguard();

        $response = $this->withoutVite()->get(route('jobs.show', $job->slug));
        $response->assertOk()->assertDontSee('<script>', false);
    }
}
