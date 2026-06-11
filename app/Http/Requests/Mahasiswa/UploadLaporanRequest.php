<?php

namespace App\Http\Requests\Mahasiswa;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

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

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {
            if (! $this->hasFile('lp') && ! $this->hasFile('lpp') && ! $this->hasFile('skp')) {
                $validator->errors()->add('lp', 'Pilih minimal satu file laporan untuk diunggah.');
            }
        });
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
