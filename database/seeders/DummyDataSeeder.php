<?php

namespace Database\Seeders;

use App\Models\ActivityLog;
use App\Models\ProposalMahasiswa;
use App\Models\User;
use Illuminate\Database\Seeder;

class DummyDataSeeder extends Seeder
{
    public function run()
    {
        // Get Dosen
        $dosen = User::where('role', 'dosen')->first();
        if (! $dosen) {
            return;
        }

        // Bikin 10 Mahasiswa PKL
        for ($i = 1; $i <= 10; $i++) {
            $user = User::create([
                'name' => 'Mahasiswa PKL '.$i,
                'username' => '1219'.rand(1000, 9999),
                'email' => 'pkl'.$i.'@gmail.com',
                'password' => bcrypt('mhs123'),
                'role' => 'mahasiswa',
                'nama_dosen_pa' => $dosen->name,
                'jenis' => 'Magang',
                'otp_verified' => true,
            ]);

            // Sebagian udah punya proposal
            if ($i > 2) {
                ProposalMahasiswa::create([
                    'user_id' => $user->id,
                    'nim' => $user->username,
                    'nama' => $user->name,
                    'jns_pkl' => 'Magang',
                    'judul_pkl' => 'Analisis dan Implementasi Sistem '.$i,
                    'tempat_riset' => 'PT. Teknologi Masa Depan '.$i,
                    'dosen_pa' => $dosen->name,
                    'nama_mentor' => 'Ibu Sari Dewi',
                    'lp' => rand(0, 1) ? 'dummy_lp.pdf' : null,
                    'lpp' => rand(0, 1) ? 'dummy_lpp.pdf' : null,
                    'skp' => rand(0, 1) ? 'dummy_skp.pdf' : null,
                    'nilai' => $i > 5 ? rand(70, 95) : 0,
                ]);
            }
        }

        // Bikin 10 Mahasiswa MSIB
        for ($i = 1; $i <= 10; $i++) {
            $user = User::create([
                'name' => 'Peserta MSIB '.$i,
                'username' => '1220'.rand(1000, 9999),
                'email' => 'msib'.$i.'@gmail.com',
                'password' => bcrypt('mhs123'),
                'role' => 'mahasiswa',
                'nama_dosen_pa' => $dosen->name,
                'jenis' => 'Program Magang khusus (PMK/GNIK/MBKM/MSIB/PMMB)',
                'otp_verified' => true,
            ]);

            // Sebagian udah punya proposal
            if ($i > 3) {
                ProposalMahasiswa::create([
                    'user_id' => $user->id,
                    'nim' => $user->username,
                    'nama' => $user->name,
                    'jns_pkl' => 'MSIB',
                    'judul_pkl' => 'Studi Independen Bersertifikat Web Dev '.$i,
                    'tempat_riset' => 'Tokopedia / Shopee '.$i,
                    'dosen_pa' => $dosen->name,
                    'nama_mentor' => 'Pak Budi MSIB',
                    'skp' => rand(0, 1) ? 'sertifikat_msib.pdf' : null,
                    'nilai' => $i > 6 ? rand(80, 100) : 0,
                ]);
            }
        }

        // Bikin Fake Activity Logs
        for ($i = 1; $i <= 25; $i++) {
            ActivityLog::create([
                'user_id' => $dosen->id,
                'kegiatan' => 'Memeriksa laporan mahasiswa ke-'.$i,
                'waktu' => now()->subMinutes(rand(1, 100)),
            ]);
        }

    }
}
