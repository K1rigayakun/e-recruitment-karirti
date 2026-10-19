<?php

namespace App\Models;

use Database\Factories\ApplicantProfileFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ApplicantProfile extends Model
{
    /** @use HasFactory<ApplicantProfileFactory> */
    use HasFactory;

    protected $fillable = [
        'slug',
        'nim',
        'study_program',
        'graduation_year',
        'phone',
        'city',
        'headline',
        'about',
        'github_url',
        'linkedin_url',
        'current_cv_document_id',
        'is_public',
    ];

    // alumni_verified_at TIDAK fillable

    protected function casts(): array
    {
        return [
            'alumni_verified_at' => 'datetime',
            'is_public' => 'boolean',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function currentCv(): BelongsTo
    {
        return $this->belongsTo(Document::class, 'current_cv_document_id');
    }

    public function portfolioProjects(): HasMany
    {
        return $this->hasMany(PortfolioProject::class)->orderBy('sort_order');
    }

    public function applications(): HasMany
    {
        return $this->hasMany(Application::class);
    }

    public function skills(): BelongsToMany
    {
        return $this->belongsToMany(Skill::class, 'applicant_skill');
    }
}
