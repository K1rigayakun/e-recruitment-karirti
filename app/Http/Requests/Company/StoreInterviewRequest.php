<?php

namespace App\Http\Requests\Company;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreInterviewRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return $this->baseRules();
    }

    protected function baseRules(): array
    {
        return [
            'scheduled_at' => ['required', 'date', 'after:'.now()->addHour()->toDateTimeString()],
            'duration_minutes' => ['required', 'integer', Rule::in([15, 30, 45, 60, 90])],
            'mode' => ['required', Rule::in(['online', 'onsite'])],
            'meeting_url' => ['nullable', 'url:https', 'required_if:mode,online'],
            'location' => ['nullable', 'string', 'max:500', 'required_if:mode,onsite'],
            'instructions' => ['nullable', 'string', 'max:1000'],
        ];
    }

    public function messages(): array
    {
        return [
            'scheduled_at.after' => 'Waktu interview minimal 1 jam dari sekarang.',
            'meeting_url.required_if' => 'URL meeting wajib diisi untuk interview online.',
            'location.required_if' => 'Lokasi wajib diisi untuk interview tatap muka.',
        ];
    }
}
