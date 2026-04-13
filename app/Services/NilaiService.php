<?php

namespace App\Services;

use App\Models\ActivityLog;
use App\Models\ProposalMahasiswa;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Mail\SystemNotification;
use Illuminate\Support\Facades\Mail;

class NilaiService
{
    /**
     * @param  array<int, int>  $formIds
     * @param  array<int, int|string>  $nilaiInputs
     */
    public function saveNilai(array $formIds, array $nilaiInputs, string $penilai, callable $scopeQuery): void
    {
        $proposals = ProposalMahasiswa::whereIn('id', $formIds)
            ->where(fn ($q) => $scopeQuery($q))
            ->get()
            ->keyBy('id');

        $proposalsToNotify = collect();

        DB::transaction(function () use ($formIds, $nilaiInputs, $penilai, $proposals, &$proposalsToNotify) {
            foreach ($formIds as $index => $formId) {
                $proposal = $proposals->get($formId);

                if (! $proposal) {
                    continue;
                }

                $oldNilai = (int) $proposal->nilai;
                $newNilai = (int) ($nilaiInputs[$index] ?? 0);

                if ($oldNilai !== $newNilai) {
                    $proposal->update([
                        'nilai' => $newNilai,
                        'penilai' => $penilai,
                    ]);

                    // Hanya kirim notif jika nilai sebelumnya kosong/0
                    if ($oldNilai === 0 && $newNilai > 0) {
                        $proposalsToNotify->push($proposal);
                    }
                }
            }

            if ($proposalsToNotify->isNotEmpty()) {
                ActivityLog::log(Auth::user()?->id, 'Input nilai per baris');
                // Notify Students ONLY for first-time grading
                $this->sendGradeNotifications($proposalsToNotify);
            }
        });
    }

    /**
     * Send email notifications to students after grades are updated.
     */
    protected function sendGradeNotifications($proposals): void
    {
        foreach ($proposals as $proposal) {
            try {
                $mahasiswa = $proposal->user;
                if ($mahasiswa && $mahasiswa->email) {
                    Mail::to($mahasiswa->email)->send(new SystemNotification(
                        'Update Nilai PKL - ' . $mahasiswa->name,
                        'Nilai PKL Telah Keluar',
                        "Halo {$mahasiswa->name}, nilai PKL Anda untuk judul '{$proposal->judul_pkl}' telah diinput oleh {$proposal->penilai}. Silakan cek dashboard untuk melihat detail nilai.",
                        route('mahasiswa.proposal.index')
                    ));
                }
            } catch (\Exception $e) {
                \Log::error('Failed to send grade notification to student: ' . $e->getMessage());
            }
        }
    }

    public function validateProposalOwnership(ProposalMahasiswa $proposal, User $user): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        if ($user->isDosen()) {
            return $proposal->user && $proposal->user->nama_dosen_pa === $user->name;
        }

        if ($user->isMentor()) {
            return $proposal->email_mentor === $user->username;
        }

        return false;
    }
}
