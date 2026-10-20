<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\ValidationRule;

/**
 * Validasi URL GitHub. Mencegah SSRF — aplikasi tidak pernah fetch URL ini dari server.
 * requireRepo=true untuk project portofolio (butuh username/repo).
 * requireRepo=false untuk link profil (cukup username).
 */
class GithubUrl implements ValidationRule
{
    public function __construct(private readonly bool $requireRepo = false) {}

    public function validate(string $attribute, mixed $value, \Closure $fail): void
    {
        if (! is_string($value) || str_contains($value, ' ')) {
            $fail('Tautan GitHub tidak valid. Contoh: https://github.com/username');

            return;
        }

        $user = '[A-Za-z0-9](?:[A-Za-z0-9-]{0,38})';
        $repo = '[A-Za-z0-9._-]{1,100}';
        $pattern = $this->requireRepo
            ? "#^https://(www\.)?github\.com/{$user}/{$repo}/?$#"
            : "#^https://(www\.)?github\.com/{$user}(/{$repo})?/?$#";

        if (! preg_match($pattern, $value)) {
            $fail('Tautan GitHub tidak valid. Contoh: https://github.com/username/nama-repo');
        }
    }
}
