<?php

namespace App\Http\Requests\Company;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class DecideApplicationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // policy di-cek di controller
    }

    public function rules(): array
    {
        return [
            'decision' => ['required', Rule::in(['accepted', 'rejected'])],
            'note' => ['nullable', 'string', 'max:500'],
        ];
    }
}
