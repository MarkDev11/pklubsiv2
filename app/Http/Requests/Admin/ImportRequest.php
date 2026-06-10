<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class ImportRequest extends FormRequest
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
            'upload_excel' => ['required', 'file', 'mimes:xlsx,xls,csv', 'max:10240'],
            'password_default_confirm' => ['accepted'],
        ];
    }

    public function messages(): array
    {
        return [
            'upload_excel.required' => 'File Excel wajib diunggah.',
            'upload_excel.mimes' => 'File harus berformat xlsx, xls, atau csv.',
            'upload_excel.max' => 'Ukuran file maksimal 10MB.',
            'password_default_confirm.accepted' => 'Konfirmasi penggunaan username sebagai password awal wajib dicentang sebelum import.',
        ];
    }
}
