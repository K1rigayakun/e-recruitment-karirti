<?php

namespace App\Http\Requests\Applicant;

use App\Rules\GithubUrl;
use Illuminate\Foundation\Http\FormRequest;

class UpdateApplicantProfileRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // middleware role:applicant sudah memastikan
    }

    public function rules(): array
    {
        return [
            'headline' => ['nullable', 'string', 'max:120'],
            'about' => ['nullable', 'string', 'max:1000'],
            'phone' => ['nullable', 'string', 'max:20', 'regex:/^[0-9+\-\s\(\)]{7,20}$/'],
            'city' => ['nullable', 'string', 'max:100'],
            'graduation_year' => ['nullable', 'integer', 'min:2000', 'max:'.date('Y', strtotime('+1 year'))],
            'nim' => ['nullable', 'string', 'max:20', 'alpha_num'],
            'github_url' => ['nullable', 'string', new GithubUrl(requireRepo: false)],
            'linkedin_url' => [
                'nullable',
                'url:https',
                function (string $attribute, mixed $value, \Closure $fail): void {
                    if ($value) {
                        $host = parse_url((string) $value, PHP_URL_HOST);
                        if (! $host || ! preg_match('/^(www\.)?linkedin\.com$/i', $host)) {
                            $fail('Tautan LinkedIn harus mengarah ke situs linkedin.com.');
                        }
                    }
                },
            ],
            'skills' => ['nullable', 'array', 'max:15'],
            'skills.*' => ['integer', 'exists:skills,id'],
            'is_public' => ['boolean'],
        ];
    }
}
