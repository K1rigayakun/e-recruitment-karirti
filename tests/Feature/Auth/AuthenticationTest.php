<?php

namespace Tests\Feature\Auth;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_redirects_to_correct_dashboard(): void
    {
        $user = User::factory()->create([
            'email' => 'pelamar@test.com',
            'password' => 'Password123!',
            'role' => UserRole::Applicant,
        ]);
        $user->email_verified_at = now();
        $user->save();

        $response = $this->post(route('login'), [
            'email' => 'pelamar@test.com',
            'password' => 'Password123!',
        ]);

        $response->assertRedirect(route('applicant.dashboard'));
    }

    public function test_login_fails_with_wrong_password(): void
    {
        User::factory()->create(['email' => 'user@test.com', 'password' => 'Password123!']);

        $response = $this->post(route('login'), [
            'email' => 'user@test.com',
            'password' => 'WrongPass!',
        ]);

        $response->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    public function test_logout_invalidates_session(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->post(route('logout'));

        $response->assertRedirect(route('login'));
        $this->assertGuest();
    }

    public function test_rate_limit_after_five_failed_attempts(): void
    {
        User::factory()->create(['email' => 'victim@test.com', 'password' => 'Password123!']);

        for ($i = 0; $i < 5; $i++) {
            $this->post(route('login'), ['email' => 'victim@test.com', 'password' => 'Wrong!']);
        }

        // Percobaan ke-6 harus 429
        $response = $this->post(route('login'), ['email' => 'victim@test.com', 'password' => 'Wrong!']);
        $response->assertSessionHasErrors('email');
        // Pesan error harus mengandung info throttle
        $errors = session('errors');
        $this->assertStringContainsStringIgnoringCase(
            'banyak',
            $errors->first('email')
        );
    }

    public function test_inactive_account_is_rejected(): void
    {
        $user = User::factory()->create([
            'email' => 'inactive@test.com',
            'password' => 'Password123!',
            'is_active' => false,
        ]);
        $user->email_verified_at = now();
        $user->save();

        $this->actingAs($user);

        // Middleware active harus kick user keluar
        $response = $this->get(route('applicant.dashboard'));
        $response->assertRedirect(route('login'));
    }

    public function test_guest_redirected_to_login_when_accessing_panel(): void
    {
        $this->get(route('applicant.dashboard'))->assertRedirect(route('login'));
        $this->get(route('company.dashboard'))->assertRedirect(route('login'));
        $this->get(route('admin.dashboard'))->assertRedirect(route('login'));
    }

    public function test_wrong_role_gets_403(): void
    {
        $applicant = User::factory()->create(['role' => UserRole::Applicant]);
        $applicant->email_verified_at = now();
        $applicant->save();

        $this->actingAs($applicant)
            ->get(route('admin.dashboard'))
            ->assertForbidden();
    }
}
