<?php

namespace App\Http\Requests\Applicant;

use App\Enums\UserRole;
use App\Models\Document;
use Illuminate\Foundation\Http\FormRequest;

class StoreApplicationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->role === UserRole::Applicant;
    }

    public function rules(): array
    {
        return [
            'cover_letter' => ['nullable', 'string', 'max:1500'],
            'cv_document_id' => [
                'nullable',
                'integer',
                // Validasi: CV milik pelamar ini dan bertipe cv
                function (string $attr, mixed $value, \Closure $fail) {
                    if (! $value) {
                        return; // gunakan CV aktif
                    }
                    $doc = Document::find($value);
                    if (! $doc
                        || $doc->user_id !== $this->user()->id
                        || $doc->type !== 'cv'
                    ) {
                        $fail('CV yang dipilih tidak valid.');
                    }
                },
            ],
        ];
    }
}
