<?php

namespace App\Models;

use App\Enums\ApplicationStatus;
use Database\Factories\ApplicationFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Application extends Model
{
    /** @use HasFactory<ApplicationFactory> */
    use HasFactory;

    protected $fillable = [
        'job_posting_id',
        'applicant_profile_id',
        'cv_document_id',
        'cover_letter',
    ];

    // status, viewed_at, decided_at, company_note TIDAK fillable

    protected function casts(): array
    {
        return [
            'status' => ApplicationStatus::class,
            'viewed_at' => 'datetime',
            'decided_at' => 'datetime',
        ];
    }

    public function jobPosting(): BelongsTo
    {
        return $this->belongsTo(JobPosting::class);
    }

    public function applicantProfile(): BelongsTo
    {
        return $this->belongsTo(ApplicantProfile::class);
    }

    public function cvDocument(): BelongsTo
    {
        return $this->belongsTo(Document::class, 'cv_document_id');
    }

    public function statusHistories(): HasMany
    {
        return $this->hasMany(ApplicationStatusHistory::class)->orderBy('created_at');
    }

    public function interviews(): HasMany
    {
        return $this->hasMany(Interview::class);
    }

    /**
     * Scope lamaran milik company profile tertentu.
     */
    public function scopeForCompany(Builder $query, int $companyProfileId): Builder
    {
        return $query->whereHas(
            'jobPosting',
            fn (Builder $q) => $q->where('company_profile_id', $companyProfileId)
        );
    }
}
