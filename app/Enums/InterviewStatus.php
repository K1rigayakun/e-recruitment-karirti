<?php

namespace App\Enums;

enum InterviewStatus: string
{
    case Scheduled = 'scheduled';
    case Confirmed = 'confirmed';
    case RescheduleRequested = 'reschedule_requested';
    case Completed = 'completed';
    case Cancelled = 'cancelled';

    public function label(): string
    {
        return match ($this) {
            self::Scheduled => 'Terjadwal',
            self::Confirmed => 'Dikonfirmasi',
            self::RescheduleRequested => 'Jadwal ulang diminta',
            self::Completed => 'Selesai',
            self::Cancelled => 'Dibatalkan',
        };
    }
}
