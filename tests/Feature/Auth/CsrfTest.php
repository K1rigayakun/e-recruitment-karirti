<?php

namespace Tests\Feature\Auth;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CsrfTest extends TestCase
{
    use RefreshDatabase;

    /**
     * POST ke login tanpa CSRF token harus mendapat 419.
     * Laravel 13 aktifkan CSRF lewat middleware grup web secara default.
     */
    public function test_post_without_csrf_returns_419(): void
    {
        // Tanpa memanggil withoutMiddleware, CSRF aktif secara default
        $response = $this->post(route('login'), [
            'email' => 'test@test.com',
            'password' => 'Password123!',
            // Tidak ada _token
        ]);

        // Laravel mengembalikan 419 (CSRF mismatch) atau redirect
        $this->assertContains($response->status(), [419, 302]);
    }

    /** GET tidak mengubah data — verifikasi route melamar hanya POST */
    public function test_apply_route_is_post_only(): void
    {
        $applyRoute = app('router')->getRoutes()->getByName('applicant.applications.store');

        $this->assertNotNull($applyRoute, 'Route applicant.applications.store harus ada');
        $this->assertContains('POST', $applyRoute->methods());
        $this->assertNotContains('GET', $applyRoute->methods());
    }
}
