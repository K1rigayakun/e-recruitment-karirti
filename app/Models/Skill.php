<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Skill extends Model
{
    public $timestamps = false;

    protected $fillable = ['name', 'slug'];

    public function applicantProfiles(): BelongsToMany
    {
        return $this->belongsToMany(ApplicantProfile::class, 'applicant_skill');
    }

    public function jobPostings(): BelongsToMany
    {
        return $this->belongsToMany(JobPosting::class, 'job_skill');
    }
}
