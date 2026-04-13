<?php

namespace App\Services;

use App\Models\ActivityLog;
use App\Models\ErrorLog;
use App\Models\OpeningHour;
use App\Models\ProposalMahasiswa;
use App\Models\User;
use Illuminate\Support\Facades\Cache;

class DashboardService
{
    /**
     * @return array<string, int>
     */
    public function getAdminStats(): array
    {
        return Cache::remember('dashboard.admin', 60, function () {
            $jumlahMhs = User::mahasiswa()->count();
            $sudahInput = ProposalMahasiswa::count();
            $totalErrors = ErrorLog::count();
            $errorsToday = ErrorLog::whereDate('created_at', today())->count();
            $totalActivities = ActivityLog::count();
            $activitiesToday = ActivityLog::whereDate('waktu', today())->count();

            return [
                'jumlahMhs' => $jumlahMhs,
                'jumlahDosen' => User::dosen()->count(),
                'jumlahMentor' => User::mentor()->count(),
                'jumlahAdmin' => User::admin()->count(),
                'totalAkun' => User::count(),
                'sudahInput' => $sudahInput,
                'belumInput' => $jumlahMhs - $sudahInput,
                'totalErrors' => $totalErrors,
                'errorsToday' => $errorsToday,
                'totalActivities' => $totalActivities,
                'activitiesToday' => $activitiesToday,
            ];
        });
    }

    /**
     * @return array<string, int>
     */
    public function getDosenStats(string $namaDosen): array
    {
        return Cache::remember("dashboard.dosen.{$namaDosen}", 60, function () use ($namaDosen) {
            $jumlahMahasiswa = User::mahasiswa()->where('nama_dosen_pa', $namaDosen)->count();
            $belumInputForm = User::mahasiswa()
                ->where('nama_dosen_pa', $namaDosen)
                ->whereDoesntHave('proposalMahasiswa')
                ->count();
            $belumDinilai = ProposalMahasiswa::belumDinilai()
                ->byDosen($namaDosen)
                ->whereNotNull('lp')
                ->whereNotNull('lpp')
                ->whereNotNull('skp')
                ->count();
            $sudahDinilai = ProposalMahasiswa::sudahDinilai()
                ->byDosen($namaDosen)
                ->count();

            return [
                'jumlahMahasiswa' => $jumlahMahasiswa,
                'belumInputForm' => $belumInputForm,
                'belumDinilai' => $belumDinilai,
                'sudahDinilai' => $sudahDinilai,
            ];
        });
    }

    /**
     * @return array<string, int>
     */
    public function getMentorStats(string $emailMentor): array
    {
        return Cache::remember("dashboard.mentor.{$emailMentor}", 60, function () use ($emailMentor) {
            return [
                'jumlahMahasiswa' => ProposalMahasiswa::where('email_mentor', $emailMentor)->count(),
                'belumDinilai' => ProposalMahasiswa::belumDinilai()->where('email_mentor', $emailMentor)->count(),
                'sudahDinilai' => ProposalMahasiswa::sudahDinilai()->where('email_mentor', $emailMentor)->count(),
            ];
        });
    }

    public function getOpeningHours(): ?OpeningHour
    {
        $openingHours = Cache::get('opening_hours');

        if ($openingHours instanceof \__PHP_Incomplete_Class) {
            Cache::forget('opening_hours');
            $openingHours = null;
        }

        return $openingHours ?: Cache::remember('opening_hours', 120, fn () => OpeningHour::first());
    }

    public function getListMahasiswa(User $user): object
    {
        $key = "dosen.list_mahasiswa.{$user->id}";
        $list = Cache::get($key);

        if ($list instanceof \__PHP_Incomplete_Class) {
            Cache::forget($key);
            $list = null;
        }

        return $list ?: Cache::remember($key, 120, fn () => User::mahasiswa()
            ->where('nama_dosen_pa', $user->name)
            ->orderBy('name')
            ->get()
        );
    }

    public function getRecentLogs(int $limit = 8): object
    {
        $key = 'dashboard.recent_logs';
        $logs = Cache::get($key);

        if ($logs instanceof \__PHP_Incomplete_Class) {
            Cache::forget($key);
            $logs = null;
        }

        return $logs ?: Cache::remember($key, 30, fn () => ActivityLog::with('user')->latest('waktu')->take($limit)->get());
    }
}
