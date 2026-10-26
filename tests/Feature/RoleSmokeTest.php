<?php

namespace Tests\Feature;

use App\Enums\CompanyVerificationStatus;
use App\Enums\UserRole;
use App\Models\ApplicantProfile;
use App\Models\CompanyProfile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RoleSmokeTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_can_access_public_routes(): void
    {
        $routes = [
            route('home'),
            route('jobs.index'),
            route('login'),
            route('register.applicant'),
            route('register.company'),
            route('privacy'),
        ];

        foreach ($routes as $url) {
            $response = $this->get($url);
            $response->assertOk();
        }
    }

    public function test_admin_can_access_admin_dashboard_and_reports(): void
    {
        $admin = User::factory()->create([
            'role' => UserRole::Admin,
            'email_verified_at' => now(),
            'is_active' => true,
        ]);

        $routes = [
            route('admin.dashboard'),
            route('admin.companies.index'),
            route('admin.jobs.index'),
            route('admin.users.index'),
            route('admin.reports.index'),
            route('admin.audit.index'),
        ];

        foreach ($routes as $url) {
            $response = $this->actingAs($admin)->get($url);
            $response->assertOk();
        }
    }

    public function test_company_can_access_company_pages(): void
    {
        $companyUser = User::factory()->create([
            'role' => UserRole::Company,
            'email_verified_at' => now(),
            'is_active' => true,
        ]);

        CompanyProfile::factory()->create([
            'user_id' => $companyUser->id,
            'verification_status' => CompanyVerificationStatus::Approved,
        ]);

        $routes = [
            route('company.dashboard'),
            route('company.profile.edit'),
            route('company.jobs.index'),
            route('company.jobs.create'),
            route('company.interviews.index'),
            route('company.reports.index'),
        ];

        foreach ($routes as $url) {
            $response = $this->actingAs($companyUser)->get($url);
            $response->assertOk();
        }
    }

    public function test_applicant_can_access_applicant_pages(): void
    {
        $applicantUser = User::factory()->create([
            'role' => UserRole::Applicant,
            'email_verified_at' => now(),
            'is_active' => true,
        ]);

        ApplicantProfile::factory()->create([
            'user_id' => $applicantUser->id,
        ]);

        $routes = [
            route('applicant.dashboard'),
            route('applicant.profile.edit'),
            route('applicant.cv.index'),
            route('applicant.projects.index'),
            route('applicant.applications.index'),
        ];

        foreach ($routes as $url) {
            $response = $this->actingAs($applicantUser)->get($url);
            $response->assertOk();
        }
    }
}
