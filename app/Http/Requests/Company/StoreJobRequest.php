<?php

namespace App\Http\Requests\Company;

use App\Enums\JobType;
use App\Enums\WorkMode;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreJobRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return $this->baseRules();
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'show_salary' => $this->boolean('show_salary'),
        ]);
    }

    protected function baseRules(): array
    {
        return [
            'title' => ['required', 'string', 'max:150'],
            'summary' => ['nullable', 'string', 'max:220'],
            'description' => ['required', 'string', 'max:5000'],
            'requirements' => ['required', 'string', 'max:3000'],
            'location' => ['required', 'string', 'max:100'],
            'job_type' => ['required', Rule::enum(JobType::class)],
            'work_mode' => ['required', Rule::enum(WorkMode::class)],
            'salary_min' => ['nullable', 'integer', 'min:0'],
            'salary_max' => ['nullable', 'integer', 'min:0', 'gte:salary_min'],
            'show_salary' => ['boolean'],
            'deadline_at' => ['nullable', 'date', 'after_or_equal:today'],
            'skills' => ['nullable', 'array', 'max:12'],
            'skills.*' => ['integer', 'exists:skills,id'],
        ];
    }
}
