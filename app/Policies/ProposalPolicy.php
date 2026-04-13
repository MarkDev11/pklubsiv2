<?php

namespace App\Policies;

use App\Enums\UserRole;
use App\Models\ProposalMahasiswa;
use App\Models\User;

class ProposalPolicy
{
    /**
     * Admin dapat melihat semua proposal.
     */
    public function viewAny(User $user): bool
    {
        return in_array($user->role, [
            UserRole::Admin,
            UserRole::Dosen,
            UserRole::Mentor,
        ]);
    }

    /**
     * Proposal hanya bisa dilihat oleh:
     * - Pemilik (mahasiswa)
     * - Dosen PA yang bersangkutan
     * - Mentor yang bersangkutan
     * - Admin
     */
    public function view(User $user, ProposalMahasiswa $proposal): bool
    {
        return match ($user->role) {
            UserRole::Admin => true,
            UserRole::Mahasiswa => $proposal->user_id === $user->id,
            UserRole::Dosen => $proposal->dosen_pa === $user->name,
            UserRole::Mentor => $proposal->email_mentor === $user->username,
        };
    }

    /**
     * Hanya mahasiswa pemilik yang bisa membuat/mengupdate proposal.
     */
    public function create(User $user): bool
    {
        return $user->role === UserRole::Mahasiswa;
    }

    public function update(User $user, ProposalMahasiswa $proposal): bool
    {
        return $user->role === UserRole::Mahasiswa && $proposal->user_id === $user->id && is_null($proposal->nilai);
    }

    /**
     * Menilai proposal — hanya Dosen PA, Mentor terkait, atau Admin.
     */
    public function grade(User $user, ProposalMahasiswa $proposal): bool
    {
        return match ($user->role) {
            UserRole::Admin => true,
            UserRole::Dosen => $proposal->dosen_pa === $user->name,
            UserRole::Mentor => $proposal->email_mentor === $user->username,
            default => false,
        };
    }

    /**
     * Upload laporan — hanya mahasiswa pemilik dan jika belum dinilai.
     */
    public function uploadLaporan(User $user, ProposalMahasiswa $proposal): bool
    {
        return $user->role === UserRole::Mahasiswa && $proposal->user_id === $user->id && is_null($proposal->nilai);
    }

    /**
     * Reset laporan — hanya mahasiswa pemilik dan jika belum dinilai.
     */
    public function resetLaporan(User $user, ProposalMahasiswa $proposal): bool
    {
        return $user->role === UserRole::Mahasiswa && $proposal->user_id === $user->id && is_null($proposal->nilai);
    }
}
