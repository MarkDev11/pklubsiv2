<?php

namespace App\Services;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Support\Str;

class UserService
{
    /**
     * @param  array<string, mixed>  $data
     */
    public function createAccount(array $data): User
    {
        $password = $data['password'] ?? $this->generateSecurePassword();

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

        $user->delete();
    }

    public function resetPassword(User $user, string $newPassword): void
    {
        $user->update(['password' => $newPassword]);
    }
}
