<?php

namespace App\Http\Requests;

use App\Rules\PassingGradeOrZero;
use App\Services\NilaiService;
use Illuminate\Foundation\Http\FormRequest;

class SaveNilaiRequest extends FormRequest
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
            'form_id' => ['required', 'array'],
            'form_id.*' => ['required', 'integer'],
            'nilai' => ['required', 'array'],
            'nilai.*' => ['required', 'integer', new PassingGradeOrZero()],
        ];
    }

    public function messages(): array
    {
        return [
            'form_id.required' => 'Data nilai wajib dipilih.',
            'nilai.required' => 'Nilai wajib diisi.',
            'nilai.*.integer' => 'Nilai harus berupa angka.',
            'nilai.*.required' => sprintf(
                'Nilai harus 0 (kosongkan) atau antara %d sampai 100.',
                NilaiService::MIN_PASSING_GRADE,
            ),
        ];
    }
}
