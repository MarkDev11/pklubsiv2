<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class UpdateOpeningHourRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isAdmin() ?? false;
    }

    /**
     * @return array<string, array<int, string>>
     */
    public function rules(): array
    {
        return [
            'open_time' => ['required', 'date'],
            'close_time' => ['required', 'date', 'after:open_time'],
            'open_laporan' => ['required', 'date'],
            'close_laporan' => ['required', 'date', 'after:open_laporan'],
            'open_nilai' => ['required', 'date'],
            'close_nilai' => ['required', 'date', 'after:open_nilai'],
        ];
    }
}
