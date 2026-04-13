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

        return $data['nama_dosen_pa'] ?? null;
    }

    /**
     * @param  array<string, mixed>  $data
     */
    protected function resolveJenis(array $data): ?string
    {
        if (($data['role'] ?? null) !== UserRole::Mahasiswa->value) {
            return null;
        }

        return $data['jenis'] ?? null;
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function updateAccount(User $user, array $data): User
    {
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
