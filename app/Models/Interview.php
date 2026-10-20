<?php

namespace App\Models;

use App\Enums\InterviewMode;
use App\Enums\InterviewStatus;
use Database\Factories\InterviewFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Interview extends Model
{
    /** @use HasFactory<InterviewFactory> */
    use HasFactory;

    protected $fillable = [
        'application_id',
        'scheduled_at',
        'duration_minutes',
        'mode',
        'meeting_url',
        'location',
        'instructions',
        'applicant_note',
        'created_by',
    ];

    // status TIDAK fillable

    protected function casts(): array
    {
        return [
            'mode' => InterviewMode::class,
            'status' => InterviewStatus::class,
            'scheduled_at' => 'datetime',
        ];
    }

    public function application(): BelongsTo
    {
        return $this->belongsTo(Application::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
