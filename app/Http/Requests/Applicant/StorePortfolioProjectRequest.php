<?php

namespace App\Http\Requests\Applicant;

use App\Rules\GithubUrl;
use Illuminate\Foundation\Http\FormRequest;

class StorePortfolioProjectRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:120'],
            'description' => ['required', 'string', 'max:600'],
            'github_url' => ['required', 'string', new GithubUrl(requireRepo: true)],
            'demo_url' => ['nullable', 'url:https'],
            'tech_stack' => ['required', 'array', 'max:8'],
            'tech_stack.*' => ['string', 'max:24'],
        ];
    }
}
