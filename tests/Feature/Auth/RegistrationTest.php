<?php

namespace Tests\Feature\Auth;

use App\Enums\UserRole;
use App\Models\ApplicantProfile;
use App\Models\CompanyProfile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_applicant_registration_creates_user_and_profile(): void
    {
        $response = $this->post(route('register.applicant'), [
            'name' => 'Budi Santoso',
            'email' => 'budi@test.com',
            'password' => 'Password123!',
            'password_confirmation' => 'Password123!',
            'privacy' => '1',
        ]);

        $response->assertRedirect(route('verification.notice'));

        $user = User::where('email', 'budi@test.com')->firstOrFail();
        $this->assertEquals(UserRole::Applicant, $user->role);
        $this->assertTrue($user->is_active);
        $this->assertNotNull(ApplicantProfile::where('user_id', $user->id)->first());
    }

    public function test_company_registration_creates_user_and_profile(): void
    {
        $response = $this->post(route('register.company'), [
            'name' => 'Siti HR',
            'email' => 'siti@company.com',
            'password' => 'Password123!',
            'password_confirmation' => 'Password123!',
            'company_name' => 'PT Contoh',
            'privacy' => '1',
        ]);

        $response->assertRedirect(route('verification.notice'));

        $user = User::where('email', 'siti@company.com')->firstOrFail();
        $this->assertEquals(UserRole::Company, $user->role);
        $this->assertNotNull(CompanyProfile::where('user_id', $user->id)->first());
    }

    public function test_role_admin_from_input_is_ignored(): void
    {
        $this->post(route('register.applicant'), [
            'name' => 'Hacker',
            'email' => 'hacker@test.com',
            'password' => 'Password123!',
            'password_confirmation' => 'Password123!',
            'role' => 'admin',   // field berbahaya — harus diabaikan
            'privacy' => '1',
        ]);

        $user = User::where('email', 'hacker@test.com')->first();
        if ($user) {
            $this->assertNotEquals(UserRole::Admin, $user->role);
        }
    }

    public function test_registration_requires_privacy_acceptance(): void
    {
        $response = $this->post(route('register.applicant'), [
            'name' => 'Test',
            'email' => 'test@test.com',
            'password' => 'Password123!',
            'password_confirmation' => 'Password123!',
            // privacy tidak diisi
        ]);

        $response->assertSessionHasErrors('privacy');
    }
}
