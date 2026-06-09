<?php

namespace App\Console\Commands;

use App\Mail\ReminderNotification;
use App\Mail\SystemNotification;
use App\Models\OpeningHour;
use App\Models\ProposalMahasiswa;
use App\Models\User;
use App\Services\NilaiService;
use Carbon\Carbon;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

#[Signature('pkl:check-timeline')]
#[Description('Memantau kalender akademik PKL untuk memberi broadcast dan notifikasi H-3')]
class CheckOpeningHours extends Command
{
    /**
     * Execute the console command.
     */
    public function handle(NilaiService $nilaiService)
    {
        $opening = OpeningHour::first();
        if (! $opening || ! $opening->open_time || ! $opening->close_time) {
            $this->info('Pengaturan tanggal belum lengkap. Command dihentikan.');

            return;
        }

        $today = Carbon::today();

        // 1. Cek Pembukaan Buka Akses / Identitas PKL
        $openDate = Carbon::parse($opening->open_time)->startOfDay();
        if ($today->equalTo($openDate)) {
            $this->info('Hari ini adalah pembukaan input PKL. Mengirim broadcast...');

            $mahasiswaList = User::where('role', 'mahasiswa')->get();
            foreach ($mahasiswaList as $mhs) {
                if ($mhs->email) {
                    try {
                        Mail::to($mhs->email)->send(new SystemNotification(
                            'Pendaftaran PKL UBSI Telah Dibuka!',
                            'Timeline PKL Dimulai',
                            "Halo {$mhs->name}, sistem pendaftaran dan input proposal PKL untuk periode ini telah resmi dibuka. Silakan masuk ke dashboard mahasiswa untuk mengajukan proposal sebelum batas akhir ".Carbon::parse($opening->close_time)->translatedFormat('d F Y').'.',
                            route('login')
                        ));
                    } catch (\Exception $e) {
                        \Log::error('Gagal broadcast ke: '.$mhs->email.' Error: '.$e->getMessage());
                    }
                }
            }
        }

        // 2. Cek H-3 Tutup Akses
        $closeDate = Carbon::parse($opening->close_time)->startOfDay();
        $hMinus3 = $closeDate->copy()->subDays(3);

        if ($today->equalTo($hMinus3)) {
            $this->info('Hari ini adalah H-3 Penutupan PKL. Mengirim reminder...');

            $mahasiswaList = User::where('role', 'mahasiswa')->get();
            $didaftarkan = ProposalMahasiswa::pluck('user_id')->toArray();

            foreach ($mahasiswaList as $mhs) {
                if (! in_array($mhs->id, $didaftarkan) && $mhs->email) {
                    try {
                        Mail::to($mhs->email)->send(new ReminderNotification(
                            $mhs->name,
                            $opening->close_time
                        ));
                    } catch (\Exception $e) {
                        \Log::error('Gagal reminder ke: '.$mhs->email.' Error: '.$e->getMessage());
                    }
                }
            }
        }

        $this->info('Pengecekan timeline selesai.');

        // 3. Auto-fill nilai = 75 (Sistem) untuk proposal yang masih kosong
        //    setelah close_nilai lewat. Idempoten — query hanya match nilai = 0/null.
        if ($opening->close_nilai) {
            $closeNilai = Carbon::parse($opening->close_nilai)->startOfDay();
            if ($today->greaterThan($closeNilai)) {
                $filled = $nilaiService->autoFillUnscored();
                if ($filled > 0) {
                    $this->info("Auto-fill nilai 75 (Sistem) untuk {$filled} proposal.");
                }
            }
        }
    }
}
