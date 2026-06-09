<?php

namespace App\Rules;

use App\Services\NilaiService;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

/**
 * Nilai PKL: hanya 0 (belum dinilai/kosong) atau MIN_PASSING_GRADE..100.
 * Nilai 1..MIN_PASSING_GRADE-1 ditolak agar penilai tidak bisa input
 * nilai di bawah ambang batas kelulusan.
 */
class PassingGradeOrZero implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (! is_numeric($value)) {
            $fail('Nilai harus berupa angka.');

            return;
        }

        $intValue = (int) $value;

        if ($intValue === 0) {
            return;
        }

        if ($intValue < NilaiService::MIN_PASSING_GRADE || $intValue > 100) {
            $fail(sprintf(
                'Nilai harus 0 (kosongkan) atau antara %d sampai 100.',
                NilaiService::MIN_PASSING_GRADE,
            ));
        }
    }
}
