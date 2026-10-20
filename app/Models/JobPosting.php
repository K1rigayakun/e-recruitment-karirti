<?php

namespace App\Models;

use App\Enums\CompanyVerificationStatus;
use App\Enums\JobStatus;
use App\Enums\JobType;
use App\Enums\WorkMode;
use Database\Factories\JobPostingFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class JobPosting extends Model
{
    /** @use HasFactory<JobPostingFactory> */
    use HasFactory;

    protected $fillable = [
        'company_profile_id',
        'title',
        'slug',
        'summary',
        'description',
        'requirements',
        'location',
        'job_type',
        'work_mode',
        'salary_min',
        'salary_max',
        'show_salary',
        'deadline_at',
        'admin_note',
    ];

    // status, published_at, closed_at TIDAK fillable

    protected function casts(): array
    {
        return [
            'job_type' => JobType::class,
            'work_mode' => WorkMode::class,
            'status' => JobStatus::class,
            'deadline_at' => 'date',
            'published_at' => 'datetime',
            'closed_at' => 'datetime',
            'show_salary' => 'boolean',
        ];
    }

    public function companyProfile(): BelongsTo
    {
        return $this->belongsTo(CompanyProfile::class);
    }

    public function applications(): HasMany
    {
        return $this->hasMany(Application::class);
    }

    public function skills(): BelongsToMany
    {
        return $this->belongsToMany(Skill::class, 'job_skill');
    }

    /**
     * Scope lowongan yang aktif (open, deadline belum lewat, perusahaan approved).
     */
    public function scopeOpen(Builder $query): Builder
    {
        return $query
            ->where('status', JobStatus::Open)
            ->where(fn (Builder $q) => $q
                ->whereNull('deadline_at')
                ->orWhereDate('deadline_at', '>=', now())
            )
            ->whereHas('companyProfile', fn (Builder $q) => $q
                ->where('verification_status', CompanyVerificationStatus::Approved)
            );
    }

    /**
     * Scope pencarian: judul, summary, nama perusahaan, skill.
     */
    public function scopeSearch(Builder $query, string $term): Builder
    {
        $like = '%'.escapeLike($term).'%';

        return $query->where(fn (Builder $q) => $q
            ->where('title', 'like', $like)
            ->orWhere('summary', 'like', $like)
            ->orWhereHas('companyProfile', fn (Builder $cq) => $cq->where('company_name', 'like', $like))
            ->orWhereHas('skills', fn (Builder $sq) => $sq->where('name', 'like', $like))
        );
    }
}
