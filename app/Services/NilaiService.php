<?php

namespace App\Services;

use App\Enums\UserRole;
use App\Mail\SystemNotification;
use App\Models\ActivityLog;
use App\Models\ProposalMahasiswa;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class NilaiService
{
    /**
     * Threshold minimum agar nilai dihitung sebagai final / masuk rata-rata.
     */
    public const MIN_PASSING_GRADE = 75;

    /**
     * Tag audit untuk membedakan input rater dari rekap final.
     */
    public const AUDIT_TAG_INPUT = 'nilai-input';

    public const AUDIT_TAG_FINAL = 'nilai-final';

    public const AUDIT_TAG_AUTOFILL = 'nilai-autofill';

    /**
     * @param  array<int, int>  $formIds
     * @param  array<int, int|string>  $nilaiInputs
     */
    public function saveNilai(array $formIds, array $nilaiInputs, User $rater, callable $scopeQuery): void
    {
        $proposals = ProposalMahasiswa::whereIn('id', $formIds)
            ->where(fn ($q) => $scopeQuery($q))
            ->get()
            ->keyBy('id');

        $proposalsToNotify = collect();

        DB::transaction(function () use ($formIds, $nilaiInputs, $rater, $proposals, &$proposalsToNotify) {
            foreach ($formIds as $index => $formId) {
                $proposal = $proposals->get($formId);

                if (! $proposal) {
                    continue;
                }

                $rawInput = (int) ($nilaiInputs[$index] ?? 0);
                $oldFinal = (int) $proposal->nilai;

                $this->recordRaterInput($proposal, $rater, $rawInput);
                $newFinal = $this->recalculateFinal($proposal);

                ActivityLog::log(
                    $rater->id,
                    sprintf(
                        'Input nilai %s untuk NIM %s: %d (final %d → %d, penilai: %s)',
                        $rater->role->label(),
                        $proposal->nim,
                        $rawInput,
                        $oldFinal,
                        $proposal->nilai,
                        $proposal->penilai ?? '-',
                    ),
                );

                if ($oldFinal === 0 && $newFinal > 0) {
                    $proposalsToNotify->push($proposal->fresh());
                }
            }

            if ($proposalsToNotify->isNotEmpty()) {
                $this->sendGradeNotifications($proposalsToNotify);
            }
        });
    }

    /**
     * Tulis input mentah rater ke kolom nilai/penilai supaya Owen-It Auditable
     * menulis baris audit baru. Tag audit "nilai-input" supaya recalculateFinal()
     * bisa membedakan dari hasil final/auto-fill.
     */
    protected function recordRaterInput(ProposalMahasiswa $proposal, User $rater, int $rawInput): void
    {
        $proposal->setAuditEvent('updated');
        if (method_exists($proposal, 'auditCustomTags')) {
            // Compatibility — newer Owen-It releases. Tags will fall back to
            // generateTags() if not supported.
        }

        $proposal->update([
            'nilai' => $rawInput,
            'penilai' => $this->raterLabel($rater),
        ]);
    }

    /**
     * Penilai prefix untuk record per-rater. Harus konsisten dengan
     * isRaterInputPenilai() dan recalculateFinal() agar bisa di-roundtrip.
     */
    protected function raterLabel(User $rater): string
    {
        return sprintf('%s: %s', $this->rolePrefix($rater->role), $rater->name);
    }

    protected function rolePrefix(UserRole $role): string
    {
        return match ($role) {
            UserRole::Admin => 'Admin',
            UserRole::Dosen => 'Dosen PA',
            UserRole::Mentor => 'Mentor Industri',
            default => $role->label(),
        };
    }

    /**
     * Hitung nilai final berdasarkan history audit per role.
     *
     * Aturan:
     *  - Admin override: kalau admin pernah input, nilai admin terakhir = final.
     *  - Dosen + Mentor (kedua-duanya ≥ 75) → rata-rata (round half up).
     *  - Hanya satu sisi yang ≥ 75 → nilai itu = final.
     *  - Tidak ada yang ≥ 75 → final = 0 (akan diisi auto-fill saat deadline).
     */
    public function recalculateFinal(ProposalMahasiswa $proposal): int
    {
        $latest = $this->latestRaterInputs($proposal);

        $admin = $latest->get(UserRole::Admin->value);
        $dosen = $latest->get(UserRole::Dosen->value);
        $mentor = $latest->get(UserRole::Mentor->value);

        if ($admin) {
            return $this->writeFinal(
                $proposal,
                (int) $admin['nilai'],
                sprintf('Admin: %s', $admin['user_name']),
            );
        }

        $dosenValid = $dosen && (int) $dosen['nilai'] >= self::MIN_PASSING_GRADE;
        $mentorValid = $mentor && (int) $mentor['nilai'] >= self::MIN_PASSING_GRADE;

        if ($dosenValid && $mentorValid) {
            $avg = (int) round(((int) $dosen['nilai'] + (int) $mentor['nilai']) / 2);

            return $this->writeFinal(
                $proposal,
                $avg,
                sprintf('Rata-rata: %s & %s', $dosen['user_name'], $mentor['user_name']),
            );
        }

        if ($dosenValid) {
            return $this->writeFinal(
                $proposal,
                (int) $dosen['nilai'],
                sprintf('%s: %s', $this->rolePrefix(UserRole::Dosen), $dosen['user_name']),
            );
        }

        if ($mentorValid) {
            return $this->writeFinal(
                $proposal,
                (int) $mentor['nilai'],
                sprintf('%s: %s', $this->rolePrefix(UserRole::Mentor), $mentor['user_name']),
            );
        }

        // Tidak ada nilai valid (≥ 75) — turunkan final ke 0 supaya auto-fill
        // mendeteksi sebagai belum dinilai.
        return $this->writeFinal($proposal, 0, null);
    }

    /**
     * Tulis nilai/penilai final hanya jika berbeda dari state sekarang.
     * Mencegah audit baris ganda saat input rater = final (misalnya admin override).
     */
    protected function writeFinal(ProposalMahasiswa $proposal, int $nilai, ?string $penilai): int
    {
        $current = (int) $proposal->nilai;
        $currentPenilai = $proposal->penilai;

        if ($current === $nilai && $currentPenilai === $penilai) {
            return $nilai;
        }

        $proposal->update([
            'nilai' => $nilai,
            'penilai' => $penilai,
        ]);

        return $nilai;
    }

    /**
     * Ambil input terakhir per role dari audit history. Hanya audit dengan
     * field `nilai` di new_values yang dihitung; abaikan record yang menulis
     * penilai final/auto-fill (mereka juga update kolom nilai, jadi kita filter
     * berdasarkan penilai prefix).
     *
     * Returns: collection keyed by role value with shape:
     *   ['nilai' => int, 'user_id' => int, 'user_name' => string, 'created_at' => Carbon]
     *
     * @return Collection<string, array{nilai: int, user_id: int, user_name: string, created_at: Carbon|null}>
     */
    protected function latestRaterInputs(ProposalMahasiswa $proposal): Collection
    {
        $audits = $proposal->audits()
            ->whereNotNull('user_id')
            ->orderByDesc('created_at')
            ->orderByDesc('id')
            ->get();

        /** @var Collection<string, array{nilai: int, user_id: int, user_name: string, created_at: Carbon|null}> $byRole */
        $byRole = collect();
        $userIdsToLoad = $audits->pluck('user_id')->filter()->unique()->values();
        $users = $userIdsToLoad->isEmpty()
            ? collect()
            : User::whereIn('id', $userIdsToLoad)->get()->keyBy('id');

        foreach ($audits as $audit) {
            $newValues = $audit->new_values ?? [];
            if (! array_key_exists('nilai', $newValues)) {
                continue;
            }

            $penilaiNew = $newValues['penilai'] ?? null;
            // Hanya hitung baris yang merepresentasikan input rater langsung.
            // Record final/rata-rata punya prefix "Rata-rata:" / "Sistem" — skip.
            if (! $this->isRaterInputPenilai($penilaiNew)) {
                continue;
            }

            $user = $users->get($audit->getAttribute('user_id'));
            if (! $user) {
                continue;
            }

            $role = $user->role->value;

            // Audit sudah diurutkan desc — entry pertama per role = paling baru.
            if ($byRole->has($role)) {
                continue;
            }

            $byRole->put($role, [
                'nilai' => (int) $newValues['nilai'],
                'user_id' => (int) $user->id,
                'user_name' => $user->name,
                'created_at' => $audit->created_at,
            ]);
        }

        return $byRole;
    }

    /**
     * Penilai bawaan rater = label role + nama (lihat recordRaterInput()).
     * Final hasil rekap pakai prefix "Rata-rata:" / "Sistem"; itu kita skip.
     */
    protected function isRaterInputPenilai(?string $penilai): bool
    {
        if (! $penilai) {
            return false;
        }

        foreach (UserRole::cases() as $role) {
            if (str_starts_with($penilai, $this->rolePrefix($role).':')) {
                return true;
            }
        }

        return false;
    }

    /**
     * Auto-fill 75 dengan penilai "Sistem" untuk semua proposal yang masih
     * nilai = 0. Dipanggil oleh scheduled command saat close_nilai sudah lewat.
     *
     * @return int jumlah proposal yang ter-auto-fill
     */
    public function autoFillUnscored(): int
    {
        $count = 0;

        ProposalMahasiswa::where(function ($q) {
            $q->whereNull('nilai')->orWhere('nilai', 0);
        })->chunkById(200, function ($chunk) use (&$count) {
            foreach ($chunk as $proposal) {
                $proposal->update([
                    'nilai' => self::MIN_PASSING_GRADE,
                    'penilai' => 'Sistem',
                ]);

                ActivityLog::log(
                    null,
                    sprintf(
                        'Auto-fill nilai %d (Sistem) untuk NIM %s',
                        self::MIN_PASSING_GRADE,
                        $proposal->nim,
                    ),
                );

                $count++;
            }
        });

        return $count;
    }

    /**
     * Send email notifications to students after grades are updated.
     *
     * @param  Collection<int, ProposalMahasiswa>  $proposals
     */
    protected function sendGradeNotifications(Collection $proposals): void
    {
        foreach ($proposals as $proposal) {
            try {
                $mahasiswa = $proposal->user;
                if ($mahasiswa && $mahasiswa->email) {
                    Mail::to($mahasiswa->email)->send(new SystemNotification(
                        'Update Nilai PKL - '.$mahasiswa->name,
                        'Nilai PKL Telah Keluar',
                        "Halo {$mahasiswa->name}, nilai PKL Anda untuk judul '{$proposal->judul_pkl}' telah diinput oleh {$proposal->penilai}. Silakan cek dashboard untuk melihat detail nilai.",
                        route('mahasiswa.proposal.index'),
                    ));
                }
            } catch (\Exception $e) {
                \Log::error('Failed to send grade notification to student: '.$e->getMessage());
            }
        }
    }

    public function validateProposalOwnership(ProposalMahasiswa $proposal, User $user): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        if ($user->isDosen()) {
            return $proposal->user && $proposal->user->nama_dosen_pa === $user->username;
        }

        if ($user->isMentor()) {
            return $proposal->email_mentor === $user->username;
        }

        return false;
    }
}
