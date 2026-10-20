<?php

namespace Tests\Feature\Auth;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SecurityHeadersTest extends TestCase
{
    use RefreshDatabase;

    public function test_security_headers_present_in_response(): void
    {
        $response = $this->get(route('login'));

        $response->assertHeader('X-Content-Type-Options', 'nosniff');
        $response->assertHeader('X-Frame-Options', 'DENY');
        $response->assertHeader('Referrer-Policy', 'strict-origin-when-cross-origin');
    }

    public function test_password_reset_message_is_neutral(): void
    {
        // Pesan harus sama, tidak membocorkan apakah email terdaftar
        $this->post(route('password.email'), ['email' => 'tidakada@test.com'])
            ->assertSessionHas('status');

        $status = session('status');
        // Pesan tidak boleh menyebut "email terdaftar" atau "tidak ditemukan"
        $this->assertStringNotContainsStringIgnoringCase('tidak ditemukan', $status ?? '');
        $this->assertStringNotContainsStringIgnoringCase('terdaftar', $status ?? '');
    }
}
