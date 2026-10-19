<?php

namespace App\Enums;

enum ApplicationStatus: string
{
    case Applied = 'applied';
    case Viewed = 'viewed';
    case Interview = 'interview';
    case Accepted = 'accepted';
    case Rejected = 'rejected';
    case Withdrawn = 'withdrawn';

    public function label(): string
    {
        return match ($this) {
            self::Applied => 'Dilamar',
            self::Viewed => 'Dilihat',
            self::Interview => 'Interview',
            self::Accepted => 'Diterima',
            self::Rejected => 'Ditolak',
            self::Withdrawn => 'Dibatalkan',
        };
    }

    /** Warna token desain untuk badge. */
    public function color(): string
    {
        return match ($this) {
            self::Applied => 'signal',
            self::Viewed => 'sun',
            self::Interview => 'violet',
            self::Accepted => 'mint',
            self::Rejected => 'coral',
            self::Withdrawn => 'mist',
        };
    }

    /** Status berikutnya yang diperbolehkan sesuai matriks transisi (plan.md 7.4). */
    public function allowedNext(): array
    {
        return match ($this) {
            self::Applied => [self::Viewed, self::Interview, self::Accepted, self::Rejected, self::Withdrawn],
            self::Viewed => [self::Interview, self::Accepted, self::Rejected, self::Withdrawn],
            self::Interview => [self::Accepted, self::Rejected, self::Withdrawn],
            default => [],
        };
    }

    public function canMoveTo(self $next): bool
    {
        return in_array($next, $this->allowedNext(), true);
    }

    public function isFinal(): bool
    {
        return in_array($this, [self::Accepted, self::Rejected, self::Withdrawn], true);
    }
}
