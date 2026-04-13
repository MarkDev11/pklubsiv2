<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * @property Carbon|null $open_time
 * @property Carbon|null $close_time
 * @property Carbon|null $open_laporan
 * @property Carbon|null $close_laporan
 * @property Carbon|null $open_nilai
 * @property Carbon|null $close_nilai
 */
class OpeningHour extends Model
{
    protected $fillable = [
        'open_time',
        'close_time',
        'open_laporan',
        'close_laporan',
        'open_nilai',
        'close_nilai',
    ];

    protected $casts = [
        'open_time' => 'datetime',
        'close_time' => 'datetime',
        'open_laporan' => 'datetime',
        'close_laporan' => 'datetime',
        'open_nilai' => 'datetime',
        'close_nilai' => 'datetime',
    ];

    /**
     * Cek apakah pendaftaran PKL sedang dibuka.
     */
    public function isPendaftaranBuka(): bool
    {
        if (! $this->open_time || ! $this->close_time) {
            return false;
        }

        return now()->between($this->open_time, $this->close_time);
    }

    /**
     * Cek apakah upload laporan sedang dibuka.
     */
    public function isLaporanBuka(): bool
    {
        if (! $this->open_laporan || ! $this->close_laporan) {
            return false;
        }

        return now()->between($this->open_laporan, $this->close_laporan);
    }

    /**
     * Cek apakah input nilai sedang dibuka.
     */
    public function isNilaiBuka(): bool
    {
        if (! $this->open_nilai || ! $this->close_nilai) {
            return false;
        }

        return now()->between($this->open_nilai, $this->close_nilai);
    }
}
