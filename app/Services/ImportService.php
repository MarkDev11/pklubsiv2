<?php

namespace App\Services;

use App\Enums\UserRole;
use App\Models\ActivityLog;
use App\Models\Import;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

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

    public function countDataRows(string $fullPath): int
    {
        $reader = IOFactory::createReaderForFile($fullPath);
        $reader->setReadDataOnly(true);

        $spreadsheet = $reader->load($fullPath);
        $highestRow = $spreadsheet->getSheet(0)->getHighestRow();
        $spreadsheet->disconnectWorksheets();

        return max(0, $highestRow - 1);
    }

    /**
     * @return array{processed:int, imported:int, skipped:int, completed:bool}
     */
    public function processChunk(Import $import, int $limit): array
    {
        $fullPath = storage_path('app/private/'.$import->path);

        if (! file_exists($fullPath)) {
            throw new \RuntimeException('File import tidak ditemukan. Upload ulang file import.');
        }

        $startDataRow = $import->processed_rows + 2;
        $remaining = max(0, $import->total_rows - $import->processed_rows);
        $limit = min($limit, $remaining);

        if ($limit === 0) {
            return [
                'processed' => $import->processed_rows,
                'imported' => $import->imported_count,
                'skipped' => $import->skipped_count,
                'completed' => true,
            ];
        }

        $endRow = $startDataRow + $limit - 1;
        $reader = IOFactory::createReaderForFile($fullPath);
        $reader->setReadDataOnly(true);

        $spreadsheet = $reader->load($fullPath);
        $sheet = $spreadsheet->getSheet(0);
        $validRoles = UserRole::values();
        $imported = 0;
        $skipped = 0;
        $skipDetails = $import->skip_details ?? [];

        DB::transaction(function () use ($sheet, $startDataRow, $endRow, $validRoles, &$imported, &$skipped, &$skipDetails): void {
            for ($row = $startDataRow; $row <= $endRow; $row++) {
                $result = $this->importRow($sheet, $row, $validRoles);

                if ($result === null) {
                    $imported++;

                    continue;
                }

                $skipped++;
                if (count($skipDetails) < 50) {
                    $skipDetails[] = $result;
                }
            }
        });

        $spreadsheet->disconnectWorksheets();

        $processed = min($import->total_rows, $import->processed_rows + $limit);
        $completed = $processed >= $import->total_rows;
        $totalImported = $import->imported_count + $imported;
        $totalSkipped = $import->skipped_count + $skipped;

        $import->update([
            'status' => $completed ? 'completed' : 'processing',
            'processed_rows' => $processed,
            'imported_count' => $totalImported,
            'skipped_count' => $totalSkipped,
            'skip_details' => $skipDetails,
            'completed_at' => $completed ? now() : null,
        ]);

        if ($completed) {
            ActivityLog::log($import->user_id, 'Import data: '.$import->fresh()->imported_count.' akun, skipped '.$import->fresh()->skipped_count.' rows');
        }

        return [
            'processed' => $processed,
            'imported' => $totalImported,
            'skipped' => $totalSkipped,
            'completed' => $completed,
        ];
    }

    /**
     * @param  array<int, string>  $validRoles
     * @return array{row:int, username:string, reason:string}|null
     */
    private function importRow(Worksheet $sheet, int $row, array $validRoles): ?array
    {
        $name = trim((string) ($sheet->getCell('A'.$row)->getValue() ?? ''));
        $nim = trim((string) ($sheet->getCell('B'.$row)->getValue() ?? ''));
        $password = trim((string) ($sheet->getCell('C'.$row)->getValue() ?? ''));
        $role = strtolower(trim((string) ($sheet->getCell('D'.$row)->getValue() ?? 'mahasiswa')));
        $dosenPA = trim((string) ($sheet->getCell('E'.$row)->getValue() ?? ''));
        $jenis = trim((string) ($sheet->getCell('F'.$row)->getValue() ?? ''));
        $kdLokal = trim((string) ($sheet->getCell('G'.$row)->getValue() ?? ''));

        if ($name === '' || $nim === '') {
            return ['row' => $row, 'username' => $nim, 'reason' => 'Nama atau username kosong'];
        }

        if (User::where('username', $nim)->exists()) {
            return ['row' => $row, 'username' => $nim, 'reason' => 'Username sudah ada'];
        }

        $resolvedRole = in_array($role, $validRoles, true) ? $role : UserRole::Mahasiswa->value;

        if ($resolvedRole === UserRole::Mahasiswa->value && ! User::dosen()->where('username', $dosenPA)->exists()) {
            return ['row' => $row, 'username' => $nim, 'reason' => 'Dosen PA tidak valid'];
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

        return null;
    }
}
