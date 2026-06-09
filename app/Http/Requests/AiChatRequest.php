<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AiChatRequest extends FormRequest
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
            'message' => ['required', 'string', 'max:1000'],
            'history' => ['nullable', 'array', 'max:10'],
            'history.*.role' => ['required', 'string', 'in:user,assistant'],
            'history.*.content' => ['required', 'string', 'max:2000'],
            'context' => ['nullable', 'array'],
            'context.route' => ['nullable', 'string', 'max:100'],
            'context.page' => ['nullable', 'string', 'max:100'],
        ];
    }

    public function messages(): array
    {
        return [
            'message.required' => 'Pesan wajib diisi.',
            'message.max' => 'Pesan maksimal 1000 karakter.',
            'history.max' => 'Riwayat chat maksimal 10 pesan.',
            'history.*.role.in' => 'Role harus user atau assistant.',
            'history.*.content.required' => 'Konten pesan wajib diisi.',
            'history.*.content.max' => 'Konten pesan maksimal 2000 karakter.',
        ];
    }
}
