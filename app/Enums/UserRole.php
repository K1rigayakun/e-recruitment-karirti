<?php

namespace App\Enums;

enum UserRole: string
{
    case Admin = 'admin';
    case Company = 'company';
    case Applicant = 'applicant';

    public function label(): string
    {
        return match ($this) {
            self::Admin => 'Admin',
            self::Company => 'Perusahaan',
            self::Applicant => 'Pelamar',
        };
    }
}
