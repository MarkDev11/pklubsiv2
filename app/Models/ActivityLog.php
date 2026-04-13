<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * @property int $user_id
 * @property string $kegiatan
 * @property Carbon $waktu
 * @property User|null $user
 */
class ActivityLog extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'user_id',
        'kegiatan',
        'waktu',
    ];

    protected $casts = [
        'waktu' => 'datetime',
    ];

    /**
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Helper static method untuk log aktivitas.
     */
    public static function log(?int $userId, string $kegiatan): self
    {
        return self::create([
            'user_id' => $userId,
            'kegiatan' => $kegiatan,
            'waktu' => now(),
        ]);
    }
}
