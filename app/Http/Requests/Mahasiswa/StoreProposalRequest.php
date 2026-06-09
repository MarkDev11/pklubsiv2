<?php

namespace App\Http\Requests\Mahasiswa;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

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
            'kd_lokal' => ['nullable', 'string', 'max:15'],
            'jns_pkl' => ['required', 'string', 'max:100', Rule::in(['Magang', 'Program Magang khusus (PMK/GNIK/MBKM/MSIB/PMMB)'])],
            'judul_pkl' => ['required', 'string', 'max:255'],
            'tempat_riset' => ['required', 'string', 'max:100'],
            'nama_mentor' => ['required', 'string', 'max:100'],
            'hp_mentor' => ['required', 'string', 'regex:/^0[0-9]{9,12}$/'],
            'email_mentor' => ['required', 'email', 'max:100'],
            'email_perusahaan' => ['nullable', 'email', 'max:100'],
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
            'nama.max' => 'Nama lengkap maksimal 100 karakter.',
            'kd_lokal.max' => 'Kode lokal maksimal 15 karakter.',
            'jns_pkl.required' => 'Jenis PKL wajib dipilih.',
            'jns_pkl.in' => 'Jenis PKL tidak valid.',
            'judul_pkl.required' => 'Judul PKL wajib diisi.',
            'judul_pkl.max' => 'Judul PKL maksimal 255 karakter.',
            'tempat_riset.required' => 'Nama instansi/tempat riset wajib diisi.',
            'tempat_riset.max' => 'Nama instansi maksimal 100 karakter.',
            'nama_mentor.required' => 'Nama mentor industri wajib diisi.',
            'nama_mentor.max' => 'Nama mentor maksimal 100 karakter.',
            'hp_mentor.required' => 'Nomor HP mentor wajib diisi.',
            'hp_mentor.regex' => 'Nomor HP mentor harus diawali 0 dan hanya berisi angka (contoh: 081234567890).',
            'email_mentor.required' => 'Email mentor wajib diisi.',
            'email_mentor.email' => 'Format email mentor tidak valid.',
            'email_mentor.max' => 'Email mentor maksimal 100 karakter.',
            'email_perusahaan.email' => 'Format email perusahaan tidak valid.',
            'email_perusahaan.max' => 'Email perusahaan maksimal 100 karakter.',
            'skm.required' => 'File Surat Keterangan Magang (SKM) wajib diunggah.',
            'skm.file' => 'SKM harus berupa file.',
            'skm.mimes' => 'File SKM harus berformat PDF.',
            'skm.max' => 'Ukuran file SKM maksimal 40MB.',
        ];
    }

    /**
     * Nama atribut untuk pesan validasi.
     */
    public function attributes(): array
    {
        return [
            'nim' => 'NIM',
            'nama' => 'nama lengkap',
            'kd_lokal' => 'kode lokal',
            'jns_pkl' => 'jenis PKL',
            'judul_pkl' => 'judul PKL',
            'tempat_riset' => 'nama instansi',
            'nama_mentor' => 'nama mentor',
            'hp_mentor' => 'nomor HP mentor',
            'email_mentor' => 'email mentor',
            'email_perusahaan' => 'email perusahaan',
            'skm' => 'file SKM',
        ];
    }
}
