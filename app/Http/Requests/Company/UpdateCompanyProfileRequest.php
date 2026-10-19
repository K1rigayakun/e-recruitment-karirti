<?php

namespace App\Http\Requests\Company;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCompanyProfileRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // middleware role:company sudah menjaga
    }

    public function rules(): array
    {
        return [
            'company_name' => ['required', 'string', 'max:255'],
            'industry' => ['nullable', 'string', 'max:100'],
            'size' => ['nullable', 'string', 'max:20'],
            'description' => ['nullable', 'string', 'max:1500'],
            'website' => ['nullable', 'url:https'],
            'city' => ['nullable', 'string', 'max:100'],
            'address' => ['nullable', 'string', 'max:500'],
            'logo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:1024'],
        ];
    }

    public function messages(): array
    {
        return [
            'logo.mimes' => 'Logo harus berformat JPG, PNG, atau WebP (SVG tidak diizinkan).',
            'logo.max' => 'Logo tidak boleh lebih dari 1 MB.',
        ];
    }
}
