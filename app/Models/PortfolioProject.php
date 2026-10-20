<?php

namespace App\Models;

use Database\Factories\PortfolioProjectFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PortfolioProject extends Model
{
    /** @use HasFactory<PortfolioProjectFactory> */
    use HasFactory;

    protected $fillable = [
        'applicant_profile_id',
        'title',
        'description',
        'github_url',
        'demo_url',
        'tech_stack',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'tech_stack' => 'array',
        ];
    }

    public function applicantProfile(): BelongsTo
    {
        return $this->belongsTo(ApplicantProfile::class);
    }
}
