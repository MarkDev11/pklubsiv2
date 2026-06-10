<?php

namespace Database\Seeders;

use App\Models\OpeningHour;
use App\Models\ProposalMahasiswa;
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

        // Mahasiswa contoh (dengan jenis default dari admin yang di-override saat proposal)
        $mahasiswa = User::create([
            'name' => 'Ahmad Fauzi',
            'username' => '12345678',
            'email' => 'ahmad@gmail.com',
            'email_bsi' => 'ahmad.12345678@bsi.ac.id',
            'password' => 'mhs123',
            'role' => 'mahasiswa',
            'nama_dosen_pa' => '1234567890',
            'jenis' => 'Program Magang khusus (PMK/GNIK/MBKM/MSIB/PMMB)', // Admin pre-set PMK
            'kd_lokal' => '12.7A.01',
            'otp_verified' => true,
        ]);

        // Proposal untuk mahasiswa (mahasiswa override jenis dari PMK → Magang)
        // Mendemonstrasikan proposal.jns_pkl adalah authoritative, bukan user.jenis
        ProposalMahasiswa::create([
            'user_id' => $mahasiswa->id,
            'nim' => $mahasiswa->username,
            'nama' => $mahasiswa->name,
            'kd_lokal' => $mahasiswa->kd_lokal,
            'jns_pkl' => 'Magang', // Mahasiswa pilih Magang (berbeda dari default PMK)
            'judul_pkl' => 'Sistem Informasi Manajemen PKL Berbasis Web',
            'tempat_riset' => 'PT. Teknologi Indonesia',
            'nama_mentor' => 'Ibu Sari Dewi',
            'hp_mentor' => '08123456789',
            'email_mentor' => 'sari@perusahaan.com',
            'dosen_pa' => '1234567890',
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
