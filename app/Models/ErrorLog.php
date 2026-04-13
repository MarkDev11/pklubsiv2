<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int|null $user_id
 * @property string|null $type
 * @property string $message
 * @property User|null $user
 */
class ErrorLog extends Model
{
    protected $fillable = [
        'user_id', 'type', 'message', 'request_url',
        'payload', 'file', 'line', 'trace',
    ];

    /**
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
