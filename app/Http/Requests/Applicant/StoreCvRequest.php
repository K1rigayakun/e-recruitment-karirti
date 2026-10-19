<?php

namespace App\Http\Requests\Applicant;

use App\Enums\UserRole;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class StoreCvRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->role === UserRole::Applicant;
    }

    public function rules(): array
    {
        return [
            'cv' => ['required', 'file', 'mimes:pdf', 'mimetypes:application/pdf',
                'max:'.config('karirti.cv_max_kb')],
        ];
    }

    /** Cek magic bytes PDF — mencegah file .txt/.php bernama .pdf */
    public function after(): array
    {
        return [function (Validator $validator) {
            $file = $this->file('cv');
            if ($file && $file->isValid()) {
                $head = @file_get_contents($file->getRealPath(), false, null, 0, 5);
                if ($head !== '%PDF-') {
                    $validator->errors()->add('cv', 'File bukan PDF yang valid. Pastikan kamu mengunggah file PDF sungguhan.');
                }
            }
        }];
    }

    public function messages(): array
    {
        return [
            'cv.mimes' => 'CV harus berformat PDF.',
            'cv.mimetypes' => 'CV harus berformat PDF.',
            'cv.max' => 'CV tidak boleh lebih dari '.config('karirti.cv_max_kb').' KB (2 MB).',
        ];
    }
}
