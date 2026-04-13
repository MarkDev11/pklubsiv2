<?php

namespace App\Jobs;

use App\Enums\UserRole;
use App\Models\ActivityLog;
use App\Models\User;
use App\Services\UserService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use PhpOffice\PhpSpreadsheet\IOFactory;

class ImportUsersJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;

    public int $timeout = 300;

    public function __construct(
        public string $filePath,
        public int $adminId
    ) {}

    public function handle(UserService $userService): void
    {
        $fullPath = storage_path('app/private/'.$this->filePath);

        try {
            $spreadsheet = IOFactory::load($fullPath);
            $sheet = $spreadsheet->getSheet(0);
            $highestRow = $sheet->getHighestRow();

            if ($highestRow <= 1) {
                return;
            }

            $validRoles = UserRole::values();
            $count = 0;

            for ($row = 2; $row <= $highestRow; $row++) {
                $name = trim($sheet->getCell('A'.$row)->getValue() ?? '');
                $nim = trim($sheet->getCell('B'.$row)->getValue() ?? '');
                $password = trim($sheet->getCell('C'.$row)->getValue() ?? '');
                $role = strtolower(trim($sheet->getCell('D'.$row)->getValue() ?? 'mahasiswa'));
                $dosenPA = trim($sheet->getCell('E'.$row)->getValue() ?? '');
                $jenis = trim($sheet->getCell('F'.$row)->getValue() ?? '');
                $kdLokal = trim($sheet->getCell('G'.$row)->getValue() ?? '');

                if (empty($name) || empty($nim)) {
                    continue;
                }

                if (User::where('username', $nim)->exists()) {
                    continue;
                }

                $userService->createAccount([
                    'name' => $name,
                    'username' => $nim,
                    'password' => $password ?: null,
                    'role' => in_array($role, $validRoles, true) ? $role : UserRole::Mahasiswa->value,
                    'nama_dosen_pa' => $dosenPA ?: null,
                    'jenis' => $jenis ?: null,
                    'kd_lokal' => $kdLokal ?: null,
                ]);

                $count++;
            }

            ActivityLog::log($this->adminId, 'Import data: '.$count.' akun');
        } catch (\Exception $e) {
            Log::error('ImportUsersJob failed', ['message' => $e->getMessage()]);
        } finally {
            if (file_exists($fullPath)) {
                unlink($fullPath);
            }
        }
    }
}
