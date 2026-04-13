<?php

namespace App\Models;

use Database\Factories\ProposalMahasiswaFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use OwenIt\Auditing\Contracts\Auditable;

/**
 * @property int $id
 * @property int $user_id
 * @property string $nim
 * @property string $nama
 * @property string|null $kd_lokal
 * @property string $jns_pkl
 * @property string $judul_pkl
 * @property string $tempat_riset
 * @property string $nama_mentor
 * @property string $hp_mentor
 * @property string $email_mentor
 * @property string|null $email_perusahaan
 * @property string|null $dosen_pa
 * @property string|null $skm
 * @property string|null $proposal
 * @property string|null $lp
 * @property string|null $lpp
 * @property string|null $skp
 * @property int|null $nilai
 * @property string|null $penilai
 * @property User|null $user
 */
class ProposalMahasiswa extends Model implements Auditable
{
    /** @use HasFactory<ProposalMahasiswaFactory> */
    use HasFactory, \OwenIt\Auditing\Auditable;

    protected $fillable = [
        'user_id',
        'nim',
        'nama',
        'kd_lokal',
        'jns_pkl',
        'judul_pkl',
        'tempat_riset',
        'nama_mentor',
        'hp_mentor',
        'email_mentor',
        'email_perusahaan',
        'dosen_pa',
        'skm',
        'proposal',
        'lp',
        'lpp',
        'skp',
        'nilai',
        'penilai',
    ];

    protected $casts = [
        'nilai' => 'integer',
    ];

    // ---- Relationships ----

    /**
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // ---- Scopes ----

    /**
     * @param  Builder<ProposalMahasiswa>  $query
     * @return Builder<ProposalMahasiswa>
     */
    public function scopeBelumDinilai(Builder $query): Builder
    {
        return $query->where(function ($q) {
            $q->whereNull('nilai')->orWhere('nilai', 0);
        });
    }

    /**
     * @param  Builder<ProposalMahasiswa>  $query
     * @return Builder<ProposalMahasiswa>
     */
    public function scopeSudahDinilai(Builder $query): Builder
    {
        return $query->where('nilai', '>', 0);
    }

    /**
     * @param  Builder<ProposalMahasiswa>  $query
     * @return Builder<ProposalMahasiswa>
     */
    public function scopeMagang(Builder $query): Builder
    {
        return $query->where('jns_pkl', 'Magang');
    }

    /**
     * @param  Builder<ProposalMahasiswa>  $query
     * @return Builder<ProposalMahasiswa>
     */
    public function scopeMsib(Builder $query): Builder
    {
        return $query->where(function ($q) {
            $q->where('jns_pkl', '!=', 'Magang')
                ->orWhere('jns_pkl', 'like', '%PMK%')
                ->orWhere('jns_pkl', 'like', '%MSIB%');
        });
    }

    /**
     * @param  Builder<ProposalMahasiswa>  $query
     * @return Builder<ProposalMahasiswa>
     */
    public function scopeByDosen(Builder $query, string $namaDosen): Builder
    {
        return $query->whereHas('user', function ($q) use ($namaDosen) {
            $q->where('nama_dosen_pa', $namaDosen);
        });
    }

    /**
     * @param  Builder<ProposalMahasiswa>  $query
     * @return Builder<ProposalMahasiswa>
     */
    public function scopeByMentor(Builder $query, string $emailMentor): Builder
    {
        return $query->where('email_mentor', $emailMentor);
    }
}
