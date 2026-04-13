<?php

namespace App\Models;

use App\Enums\UserRole;
use Database\Factories\UserFactory;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Carbon;
use OwenIt\Auditing\Contracts\Auditable;

/**
 * @property int $id
 * @property string $name
 * @property string $username
 * @property string $email
 * @property string|null $email_bsi
 * @property UserRole $role
 * @property string|null $nama_dosen_pa
 * @property string|null $jenis
 * @property string|null $kd_lokal
 * @property string|null $phone
 * @property string|null $google_id
 * @property string|null $otp_code
 * @property Carbon|null $otp_expires_at
 * @property bool $otp_verified
 */
class User extends Authenticatable implements Auditable, MustVerifyEmail
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable, \OwenIt\Auditing\Auditable;

    protected $fillable = [
        'name',
        'username',
        'email',
        'email_bsi',
        'password',
        'role',
        'nama_dosen_pa',
        'jenis',
        'kd_lokal',
        'phone',
        'google_id',
        'otp_code',
        'otp_expires_at',
        'otp_verified',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'role' => UserRole::class,
        'otp_expires_at' => 'datetime',
        'otp_verified' => 'boolean',
    ];

    // ---- Relationships ----

    /**
     * @return HasOne<ProposalMahasiswa, $this>
     */
    public function proposalMahasiswa(): HasOne
    {
        return $this->hasOne(ProposalMahasiswa::class);
    }

    /**
     * @return HasMany<ActivityLog, $this>
     */
    public function activityLogs(): HasMany
    {
        return $this->hasMany(ActivityLog::class);
    }

    // ---- Scopes ----

    /**
     * @param  Builder<User>  $query
     * @return Builder<User>
     */
    public function scopeMahasiswa(Builder $query): Builder
    {
        return $query->where('role', UserRole::Mahasiswa->value);
    }

    /**
     * @param  Builder<User>  $query
     * @return Builder<User>
     */
    public function scopeDosen(Builder $query): Builder
    {
        return $query->where('role', UserRole::Dosen->value);
    }

    /**
     * @param  Builder<User>  $query
     * @return Builder<User>
     */
    public function scopeMentor(Builder $query): Builder
    {
        return $query->where('role', UserRole::Mentor->value);
    }

    /**
     * @param  Builder<User>  $query
     * @return Builder<User>
     */
    public function scopeAdmin(Builder $query): Builder
    {
        return $query->where('role', UserRole::Admin->value);
    }

    // ---- Helpers ----

    public function isAdmin(): bool
    {
        return $this->role->value === UserRole::Admin->value;
    }

    public function isDosen(): bool
    {
        return $this->role->value === UserRole::Dosen->value;
    }

    public function isMahasiswa(): bool
    {
        return $this->role->value === UserRole::Mahasiswa->value;
    }

    public function isMentor(): bool
    {
        return $this->role->value === UserRole::Mentor->value;
    }

    public function generateOtp(): void
    {
        $this->update([
            'otp_code' => str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT),
            'otp_expires_at' => now()->addMinutes(5),
            'otp_verified' => false,
        ]);
    }

    public function verifyOtp(string $code): bool
    {
        if (! $this->otp_code || ! $this->otp_expires_at) {
            return false;
        }

        if ($this->otp_expires_at->isPast()) {
            return false;
        }

        if (! hash_equals($this->otp_code, $code)) {
            return false;
        }

        $this->update([
            'otp_verified' => true,
            'otp_code' => null,
            'otp_expires_at' => null,
        ]);

        return true;
    }
}
