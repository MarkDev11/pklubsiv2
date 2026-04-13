<?php

namespace App\Http\Requests\Mahasiswa;

use Illuminate\Foundation\Http\FormRequest;

class StoreProposalRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Aturan validasi untuk menyimpan proposal PKL baru.
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
            'email_mentor' => ['required', 'email', 'max:100'],
            'email_perusahaan' => ['nullable', 'email', 'max:100'],
            'kd_lokal' => ['nullable', 'string', 'max:15'],
            'skm' => ['required', 'file', 'mimes:pdf', 'max:40960'],
        ];
    }

    /**
     * Pesan validasi kustom dalam Bahasa Indonesia.
     */
    public function messages(): array
    {
        return [
            'nim.required' => 'NIM wajib diisi.',
            'nim.max' => 'NIM maksimal 8 karakter.',
            'nama.required' => 'Nama lengkap wajib diisi.',
            'jns_pkl.required' => 'Jenis PKL wajib dipilih.',
            'judul_pkl.required' => 'Judul PKL wajib diisi.',
            'tempat_riset.required' => 'Nama instansi/tempat riset wajib diisi.',
            'nama_mentor.required' => 'Nama mentor industri wajib diisi.',
            'hp_mentor.required' => 'Nomor HP mentor wajib diisi.',
            'email_mentor.required' => 'Email mentor wajib diisi.',
            'email_mentor.email' => 'Format email mentor tidak valid.',
            'skm.required' => 'File Surat Keterangan Magang (SKM) wajib diunggah.',
            'skm.mimes' => 'File SKM harus berformat PDF.',
            'skm.max' => 'Ukuran file SKM maksimal 40MB.',
        ];
    }
}
