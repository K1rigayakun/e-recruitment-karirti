<?php

namespace Tests\Unit;

use App\Rules\GithubUrl;
use Illuminate\Support\Facades\Validator;
use Tests\TestCase;

class GithubUrlTest extends TestCase
{
    private function passes(string $url, bool $requireRepo = false): bool
    {
        return Validator::make(
            ['url' => $url],
            ['url' => [new GithubUrl($requireRepo)]]
        )->passes();
    }

    /** URL valid — tanpa repo (profil) */
    public function test_valid_profile_urls(): void
    {
        $this->assertTrue($this->passes('https://github.com/user'));
        $this->assertTrue($this->passes('https://github.com/user/'));
        $this->assertTrue($this->passes('https://www.github.com/user'));
        $this->assertTrue($this->passes('https://github.com/user/repo')); // repo juga valid sebagai profil
        $this->assertTrue($this->passes('https://www.github.com/user/repo/'));
    }

    /** URL valid — dengan repo (portofolio) */
    public function test_valid_repo_urls(): void
    {
        $this->assertTrue($this->passes('https://github.com/user/my-repo', true));
        $this->assertTrue($this->passes('https://github.com/user/repo.name', true));
        $this->assertTrue($this->passes('https://www.github.com/user/repo/', true));
    }

    /** URL tidak valid */
    public function test_invalid_urls(): void
    {
        // http (bukan https)
        $this->assertFalse($this->passes('http://github.com/user/repo'));
        // domain berbahaya mirip github
        $this->assertFalse($this->passes('https://github.com.evil.com/user/repo'));
        // credential injection
        $this->assertFalse($this->passes('https://github.com@evil.com/user/repo'));
        // javascript scheme
        $this->assertFalse($this->passes('javascript:alert(1)'));
        // domain lain
        $this->assertFalse($this->passes('https://evilgithub.com/user/repo'));
        // path tiga segmen
        $this->assertFalse($this->passes('https://github.com/user/repo/extra', true));
        // mengandung spasi
        $this->assertFalse($this->passes('https://github.com/user name/repo'));
    }

    /** Butuh repo tapi hanya ada username */
    public function test_require_repo_fails_without_repo(): void
    {
        $this->assertFalse($this->passes('https://github.com/user', true));
    }
}
