<?php

namespace Database\Seeders;

use App\Models\OpeningHour;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Admin default
        User::create([
            'name' => 'Administrator',
            'username' => 'admin',
            'email' => 'admin@bsi.ac.id',
            'email_bsi' => 'admin@bsi.ac.id',
            'password' => 'admin123',
            'role' => 'admin',
            'otp_verified' => true,
        ]);

        // Dosen contoh
        User::create([
            'name' => 'Dr. Budi Santoso',
            'username' => '1234567890',
            'email' => 'budi@bsi.ac.id',
            'email_bsi' => 'budi@bsi.ac.id',
            'password' => 'dosen123',
            'role' => 'dosen',
            'otp_verified' => true,
        ]);

        // Mahasiswa contoh
        User::create([
            'name' => 'Ahmad Fauzi',
            'username' => '12345678',
            'email' => 'ahmad@gmail.com',
            'email_bsi' => 'ahmad.12345678@bsi.ac.id',
            'password' => 'mhs123',
            'role' => 'mahasiswa',
            'nama_dosen_pa' => '1234567890',
            'jenis' => 'Magang',
            'kd_lokal' => '12.7A.01',
            'otp_verified' => true,
        ]);

        // Mentor contoh
        User::create([
            'name' => 'Ibu Sari Dewi',
            'username' => 'sari@perusahaan.com',
            'email' => 'sari@perusahaan.com',
            'password' => 'mentor123',
            'role' => 'mentor',
            'otp_verified' => true,
        ]);

        // Opening Hours default
        OpeningHour::create([
            'open_time' => '2026-03-01 07:00:00',
            'close_time' => '2026-07-31 23:59:00',
            'open_laporan' => '2026-06-01 07:00:00',
            'close_laporan' => '2026-07-31 23:59:00',
            'open_nilai' => '2026-07-01 07:00:00',
            'close_nilai' => '2026-08-15 23:59:00',
        ]);
    }
}
