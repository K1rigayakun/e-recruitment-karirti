<?php

namespace App\Models;

use App\Enums\CompanyVerificationStatus;
use Database\Factories\CompanyProfileFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CompanyProfile extends Model
{
    /** @use HasFactory<CompanyProfileFactory> */
    use HasFactory;

    protected $fillable = [
        'slug',
        'company_name',
        'industry',
        'size',
        'description',
        'website',
        'city',
        'address',
        'logo_path',
    ];

    // verification_status, verified_at, verification_note TIDAK fillable

    protected function casts(): array
    {
        return [
            'verification_status' => CompanyVerificationStatus::class,
            'verified_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function jobPostings(): HasMany
    {
        return $this->hasMany(JobPosting::class);
    }
}
