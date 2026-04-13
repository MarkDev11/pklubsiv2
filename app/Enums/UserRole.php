<?php

namespace App\Enums;

enum UserRole: string
{
    case Admin = 'admin';
    case Dosen = 'dosen';
    case Mahasiswa = 'mahasiswa';
    case Mentor = 'mentor';

    public function label(): string
    {
        return match ($this) {
            self::Admin => 'Administrator',
            self::Dosen => 'Dosen PA',
            self::Mahasiswa => 'Mahasiswa',
            self::Mentor => 'Mentor Industri',
        };
    }

    public function dashboardRoute(): string
    {
        return match ($this) {
            self::Admin => 'admin.dashboard',
            self::Dosen => 'dosen.dashboard',
            self::Mahasiswa => 'mahasiswa.dashboard',
            self::Mentor => 'mentor.dashboard',
        };
    }

    /**
     * @return array<string>
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
