<?php

namespace App\Http\Requests\Admin;

use App\Enums\UserRole;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreAkunRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:100'],
            'username' => ['required', 'string', 'max:100', 'unique:users,username'],
            'role' => ['required', Rule::in(UserRole::values())],
            'password' => ['nullable', 'string', 'min:8'],
            'jenis' => ['nullable', 'string', 'max:50'],
            'nama_dosen_pa' => [
                Rule::requiredIf(fn () => $this->input('role') === UserRole::Mahasiswa->value),
                'nullable',
                'string',
                'max:100',
                Rule::exists('users', 'username')->where('role', UserRole::Dosen->value),
            ],
            'kd_lokal' => ['nullable', 'string', 'max:50'],
        ];
    }

    public function messages(): array
    {
        return [
            'nama_dosen_pa.required' => 'NIP Dosen PA wajib diisi untuk mahasiswa.',
            'nama_dosen_pa.exists' => 'NIP Dosen PA tidak ditemukan atau bukan akun dosen.',
        ];
    }
}
