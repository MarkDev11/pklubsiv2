<?php

namespace App\Services;

use App\Enums\UserRole;
use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use PhpOffice\PhpSpreadsheet\IOFactory;

class ImportService
{
    public function __construct(
        protected UserService $userService
    ) {}

    public function importFromExcel(string $fullPath, int $adminId): int
    {
        $spreadsheet = IOFactory::load($fullPath);
        $sheet = $spreadsheet->getSheet(0);
        $highestRow = $sheet->getHighestRow();

        if ($highestRow <= 1) {
            return 0;
        }

        $validRoles = UserRole::values();
        $count = 0;
        $skipped = [
            'empty_required' => 0,
            'duplicate_username' => 0,
            'invalid_dosen_pa' => 0,
        ];

        for ($row = 2; $row <= $highestRow; $row++) {
            $name = trim($sheet->getCell('A'.$row)->getValue() ?? '');
            $nim = trim($sheet->getCell('B'.$row)->getValue() ?? '');
            $password = trim($sheet->getCell('C'.$row)->getValue() ?? '');
            $role = strtolower(trim($sheet->getCell('D'.$row)->getValue() ?? 'mahasiswa'));
            $dosenPA = trim($sheet->getCell('E'.$row)->getValue() ?? '');
            $jenis = trim($sheet->getCell('F'.$row)->getValue() ?? '');
            $kdLokal = trim($sheet->getCell('G'.$row)->getValue() ?? '');

            if (empty($name) || empty($nim)) {
                $skipped['empty_required']++;
                continue;
            }

            if (User::where('username', $nim)->exists()) {
                $skipped['duplicate_username']++;
                continue;
            }

            $resolvedRole = in_array($role, $validRoles, true) ? $role : UserRole::Mahasiswa->value;

            if ($resolvedRole === UserRole::Mahasiswa->value && ! User::dosen()->where('username', $dosenPA)->exists()) {
                $skipped['invalid_dosen_pa']++;
                continue;
            }

            $this->userService->createAccount([
                'name' => $name,
                'username' => $nim,
                'password' => $password ?: null,
                'role' => $resolvedRole,
                'nama_dosen_pa' => $dosenPA ?: null,
                'jenis' => $jenis ?: null,
                'kd_lokal' => $kdLokal ?: null,
            ]);

            $count++;
        }

        ActivityLog::log($adminId, 'Import data: '.$count.' akun, skipped '.array_sum($skipped).' rows');
        Log::info('ImportService summary', ['imported' => $count, 'skipped' => $skipped]);

        return $count;
    }
}
