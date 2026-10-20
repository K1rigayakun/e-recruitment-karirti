<?php

namespace App\Enums;

enum WorkMode: string
{
    case Onsite = 'onsite';
    case Hybrid = 'hybrid';
    case Remote = 'remote';

    public function label(): string
    {
        return match ($this) {
            self::Onsite => 'Di kantor',
            self::Hybrid => 'Hibrid',
            self::Remote => 'Jarak jauh',
        };
    }
}
