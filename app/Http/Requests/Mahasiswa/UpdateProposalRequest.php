<?php

namespace App\Http\Requests\Mahasiswa;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProposalRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Aturan validasi untuk memperbarui proposal PKL.
     *
     * @return array<string, array<int, string>>
     */
    public function rules(): array
    {
        return [
            'nim' => ['required', 'string', 'max:8'],
            'nama' => ['required', 'string', 'max:100'],
            'jns_pkl' => ['required', 'string'],
            'judul_pkl' => ['required', 'string', 'max:255'],
            'tempat_riset' => ['required', 'string', 'max:100'],
            'nama_mentor' => ['required', 'string', 'max:100'],
            'hp_mentor' => ['required', 'string', 'max:20'],
            'email_mentor' => ['required', 'email'],
            'email_perusahaan' => ['nullable', 'email'],
            'kd_lokal' => ['nullable', 'string'],
            'skm' => ['nullable', 'file', 'mimes:pdf', 'max:40960'],
        ];
    }

    /**
     * Pesan validasi kustom dalam Bahasa Indonesia.
     */
    public function messages(): array
    {
        return [
            'nim.required' => 'NIM wajib diisi.',
            'nama.required' => 'Nama lengkap wajib diisi.',
            'jns_pkl.required' => 'Jenis PKL wajib dipilih.',
            'judul_pkl.required' => 'Judul PKL wajib diisi.',
            'tempat_riset.required' => 'Nama instansi/tempat riset wajib diisi.',
            'nama_mentor.required' => 'Nama mentor industri wajib diisi.',
            'hp_mentor.required' => 'Nomor HP mentor wajib diisi.',
            'email_mentor.required' => 'Email mentor wajib diisi.',
            'email_mentor.email' => 'Format email mentor tidak valid.',
            'skm.mimes' => 'File SKM harus berformat PDF.',
            'skm.max' => 'Ukuran file SKM maksimal 40MB.',
        ];
    }
}
