<?php

namespace App\Enums;

enum InterviewMode: string
{
    case Online = 'online';
    case Onsite = 'onsite';

    public function label(): string
    {
        return match ($this) {
            self::Online => 'Online',
            self::Onsite => 'Di kantor',
        };
    }
}
