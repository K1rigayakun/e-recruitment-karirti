<?php

namespace App\Http\Requests\Public;

use App\Enums\JobType;
use App\Enums\WorkMode;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class JobFilterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'q' => ['nullable', 'string', 'max:80'],
            'type' => ['nullable', Rule::enum(JobType::class)],
            'mode' => ['nullable', Rule::enum(WorkMode::class)],
            'location' => ['nullable', 'string', 'max:100'],
            'skill' => ['nullable', 'array'],
            'skill.*' => ['integer', 'exists:skills,id'],
            'sort' => ['nullable', Rule::in(['latest', 'deadline'])],
        ];
    }
}
