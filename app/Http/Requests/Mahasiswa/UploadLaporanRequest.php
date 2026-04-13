<?php

namespace App\Http\Requests\Mahasiswa;

use Illuminate\Foundation\Http\FormRequest;

class UploadLaporanRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, array<int, string>>
     */
    public function rules(): array
    {
        return [
            'lp' => ['nullable', 'file', 'mimes:pdf', 'max:40960'],
            'lpp' => ['nullable', 'file', 'mimes:pdf', 'max:40960'],
            'skp' => ['nullable', 'file', 'mimes:pdf', 'max:40960'],
        ];
    }

    public function messages(): array
    {
        return [
            'lp.mimes' => 'File Laporan Akhir harus berformat PDF.',
            'lp.max' => 'Ukuran file Laporan Akhir maksimal 40MB.',
            'lpp.mimes' => 'File Lembar Penilaian harus berformat PDF.',
            'lpp.max' => 'Ukuran file Lembar Penilaian maksimal 40MB.',
            'skp.mimes' => 'File Surat Keterangan harus berformat PDF.',
            'skp.max' => 'Ukuran file Surat Keterangan maksimal 40MB.',
        ];
    }
}
