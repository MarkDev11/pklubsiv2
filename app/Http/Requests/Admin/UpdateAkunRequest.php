<?php

namespace App\Http\Requests\Admin;

use App\Enums\UserRole;
use App\Models\ProposalMahasiswa;
use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class UpdateAkunRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isAdmin() ?? false;
    }

    /**
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
    {
        $target = $this->targetUser();

        return [
            'name' => ['required', 'string', 'max:100'],
            'username' => ['required', 'string', 'max:100', Rule::unique('users', 'username')->ignore($target?->id)],
            'role' => ['required', Rule::in(UserRole::values())],
            'jenis' => ['nullable', 'string'],
            'nama_dosen_pa' => [
                Rule::requiredIf(fn () => $this->input('role') === UserRole::Mahasiswa->value),
                'nullable',
                'string',
                'max:100',
                Rule::exists('users', 'username')->where('role', UserRole::Dosen->value),
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Nama wajib diisi.',
            'username.required' => 'Username wajib diisi.',
            'username.unique' => 'Username sudah digunakan.',
            'role.required' => 'Peran wajib dipilih.',
            'role.in' => 'Peran tidak valid.',
            'nama_dosen_pa.required' => 'NIP Dosen PA wajib diisi untuk mahasiswa.',
            'nama_dosen_pa.exists' => 'NIP Dosen PA tidak ditemukan atau bukan akun dosen.',
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {
            $target = $this->targetUser();

            if (! $target) {
                return;
            }

            $hasDosenRelations = User::mahasiswa()->where('nama_dosen_pa', $target->username)->exists()
                || ProposalMahasiswa::where('dosen_pa', $target->username)->exists();
            $hasMentorRelations = ProposalMahasiswa::where('email_mentor', $target->username)->exists();
            $hasMahasiswaRelations = ProposalMahasiswa::where('nim', $target->username)->exists()
                || ProposalMahasiswa::where('user_id', $target->id)->exists();
            $hasUsernameRelations = $hasDosenRelations || $hasMentorRelations || $hasMahasiswaRelations;

            if ($hasUsernameRelations && $target->username !== $this->input('username')) {
                $validator->errors()->add('username', 'Username tidak dapat diubah karena sudah direferensikan data PKL.');
            }

            if ($hasUsernameRelations && $target->role->value !== $this->input('role')) {
                $validator->errors()->add('role', 'Role tidak dapat diubah karena akun sudah memiliki relasi data PKL.');
            }
        });
    }

    protected function targetUser(): ?User
    {
        $encrypted = $this->route('encrypted');

        if (! is_string($encrypted)) {
            return null;
        }

        return User::where('username', decryptUrl($encrypted))->first();
    }
}
