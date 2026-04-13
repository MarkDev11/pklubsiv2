<?php

namespace App\Enums;

enum PklType: string
{
    case Magang = 'Magang';
    case MSIB = 'Program Magang khusus (PMK/GNIK/MBKM/MSIB/PMMB)';

    /**
     * Label singkat untuk tampilan UI.
     */
    public function label(): string
    {
        return match ($this) {
            self::Magang => 'Magang Reguler',
            self::MSIB => 'MSIB / Kampus Merdeka',
        };
    }

    /**
     * Daftar semua nilai string enum.
     *
     * @return array<string>
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
