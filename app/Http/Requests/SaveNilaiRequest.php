<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SaveNilaiRequest extends FormRequest
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
            'form_id' => ['required', 'array'],
            'form_id.*' => ['required', 'integer'],
            'nilai' => ['required', 'array'],
            'nilai.*' => ['required', 'integer', 'min:0', 'max:100'],
        ];
    }

    public function messages(): array
    {
        return [
            'form_id.required' => 'Data nilai wajib dipilih.',
            'nilai.required' => 'Nilai wajib diisi.',
            'nilai.*.min' => 'Nilai minimal 0.',
            'nilai.*.max' => 'Nilai maksimal 100.',
        ];
    }
}
