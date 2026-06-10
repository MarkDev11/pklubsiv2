<?php

namespace App\Services;

use App\Enums\UserRole;
use App\Models\ActivityLog;
use App\Models\ProposalMahasiswa;
use App\Models\User;
use Illuminate\Support\Str;

class UserService
{
    /**
     * @param  array<string, mixed>  $data
     */
    public function createAccount(array $data): User
    {
        $password = ($data['password'] ?? null) ?: $data['username'];

        $user = User::create([
            'name' => $data['name'],
            'username' => $data['username'],
            'email' => $this->generateEmail($data['username']),
            'password' => $password,
            'role' => $data['role'],
            'nama_dosen_pa' => $this->resolveDosenPa($data),
            'jenis' => $this->resolveJenis($data),
            'kd_lokal' => $data['kd_lokal'] ?? null,
        ]);

        return $user;
    }

    public function generateSecurePassword(): string
    {
        return Str::password(12, letters: true, numbers: true, symbols: true);
    }

    public function generateEmail(string $username): string
    {
        return $username.'@bsi.ac.id';
    }

    /**
     * @param  array<string, mixed>  $data
     */
    protected function resolveDosenPa(array $data): ?string
    {
        if (($data['role'] ?? null) !== UserRole::Mahasiswa->value) {
            return null;
        }

        $dosenPa = trim((string) ($data['nama_dosen_pa'] ?? ''));

        return $dosenPa !== '' ? $dosenPa : null;
    }

    /**
     * Resolve jenis PKL (optional field).
     * Jenis PKL seharusnya dipilih mahasiswa saat submit proposal, bukan saat create user.
     * Field ini optional dan hanya untuk legacy/kompatibilitas.
     *
     * @param  array<string, mixed>  $data
     */
    protected function resolveJenis(array $data): ?string
    {
        if (($data['role'] ?? null) !== UserRole::Mahasiswa->value) {
            return null;
        }

        // Return jenis if provided, null if not (field is optional)
        return $data['jenis'] ?? null;
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function updateAccount(User $user, array $data): User
    {
        $data['nama_dosen_pa'] = $this->resolveDosenPa($data);
        $data['jenis'] = $this->resolveJenis($data);

        $user->update($data);

        return $user;
    }

    public function deleteAccount(User $user): void
    {
        if ($user->isAdmin()) {
            throw new \Exception('Akun admin tidak dapat dihapus.');
        }

        if ($this->hasSystemRelations($user)) {
            throw new \Exception('Akun tidak dapat dihapus karena sudah memiliki data atau relasi PKL.');
        }

        $user->delete();
    }

    public function resetPassword(User $user, string $newPassword): void
    {
        $user->update(['password' => $newPassword]);
    }

    public function hasSystemRelations(User $user): bool
    {
        if (ActivityLog::where('user_id', $user->id)->exists()) {
            return true;
        }

        if ($user->isMahasiswa() && ProposalMahasiswa::where('user_id', $user->id)->exists()) {
            return true;
        }

        if ($user->isDosen()) {
            return User::mahasiswa()->where('nama_dosen_pa', $user->username)->exists()
                || ProposalMahasiswa::where('dosen_pa', $user->username)->exists();
        }

        if ($user->isMentor()) {
            return ProposalMahasiswa::where('email_mentor', $user->username)->exists();
        }

        return false;
    }

    public function usernameHasRelations(User $user): bool
    {
        return ProposalMahasiswa::where('dosen_pa', $user->username)->exists()
            || User::mahasiswa()->where('nama_dosen_pa', $user->username)->exists()
            || ProposalMahasiswa::where('email_mentor', $user->username)->exists()
            || ProposalMahasiswa::where('nim', $user->username)->exists();
    }
}
