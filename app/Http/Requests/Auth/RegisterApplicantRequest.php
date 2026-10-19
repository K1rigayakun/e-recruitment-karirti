<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class RegisterApplicantRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'confirmed', Password::defaults()],
            'graduation_year' => ['nullable', 'integer', 'min:2000', 'max:'.date('Y', strtotime('+1 year'))],
            'nim' => ['nullable', 'string', 'max:20', 'alpha_num'],
            'privacy' => ['accepted'],
        ];
    }

    public function messages(): array
    {
        return [
            'privacy.accepted' => 'Kamu harus menyetujui kebijakan privasi untuk mendaftar.',
        ];
    }
}
