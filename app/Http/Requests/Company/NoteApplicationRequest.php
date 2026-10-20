<?php

namespace App\Http\Requests\Company;

use Illuminate\Foundation\Http\FormRequest;

class NoteApplicationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'company_note' => ['required', 'string', 'max:1000'],
        ];
    }
}
