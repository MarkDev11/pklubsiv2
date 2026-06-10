<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class LargeScaleDataSeeder extends Seeder
{
    private $namaDepan = ['Ahmad', 'Siti', 'Budi', 'Dewi', 'Eko', 'Fitri', 'Hadi', 'Indah', 'Joko', 'Kartika', 'Lukman', 'Maya', 'Nur', 'Putri', 'Rudi', 'Sari', 'Taufik', 'Umi', 'Wati', 'Yuni', 'Agus', 'Rina', 'Doni', 'Lina', 'Fajar'];
    private $namaBelakang = ['Pratama', 'Wulandari', 'Santoso', 'Rahayu', 'Saputra', 'Anggraini', 'Setiawan', 'Lestari', 'Wijaya', 'Kusuma', 'Firmansyah', 'Safitri', 'Hidayat', 'Nurjanah', 'Permana'];
    private $companies = ['PT Teknologi Indonesia', 'CV Digital Solutions', 'PT Maju Bersama', 'Tokopedia', 'Shopee', 'Bank Mandiri', 'BCA', 'Telkom Indonesia', 'Rumah Sakit Umum', 'PT Sejahtera', 'Gojek', 'Bukalapak', 'Traveloka', 'PT Indofood', 'Pertamina'];
    
    private $dosen = [];
    private $mentors = [];
    private $userIdCounter = 1;
    private $hashedPassword;
    
    public function run()
    {
        ini_set('memory_limit', '1G');
        DB::connection()->disableQueryLog();
        
        // Pre-hash password once for performance
        $this->hashedPassword = Hash::make('password123');
        
        $this->command->info('🚀 Starting large scale data seeding...');
        $this->command->newLine();
        
        $this->createDosen();
        $this->createMentors();
        $this->createMahasiswaWithProposals();
        
        $this->command->newLine();
        $this->command->info('✅ Seeding completed!');
        $this->showStats();
    }
    
    private function createDosen()
    {
        $this->command->info('📚 Creating 400 dosen...');
        
        $batch = [];
        for ($i = 1; $i <= 400; $i++) {
            $name = 'Dr. ' . $this->randomName();
            $username = 'DSN' . str_pad($i, 6, '0', STR_PAD_LEFT);
            
            $batch[] = [
                'name' => $name,
                'username' => $username,
                'email' => 'dosen' . $i . '@bsi.ac.id',
                'email_bsi' => 'dosen' . $i . '@bsi.ac.id',
                'password' => $this->hashedPassword,
                'role' => 'dosen',
                'otp_verified' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ];
            
            $this->dosen[] = $username;
            
            if (count($batch) >= 100) {
                DB::table('users')->insert($batch);
                $batch = [];
            }
        }
        
        if (!empty($batch)) {
            DB::table('users')->insert($batch);
        }
        
        $this->command->info('✅ 400 dosen created');
    }
    
    private function createMentors()
    {
        $this->command->info('👔 Creating 2,000 mentors...');
        
        $batch = [];
        for ($i = 1; $i <= 2000; $i++) {
            $name = (rand(0, 1) ? 'Bpk. ' : 'Ibu ') . $this->randomName();
            $username = 'mentor' . str_pad($i, 4, '0', STR_PAD_LEFT) . '@perusahaan.com';
            
            $batch[] = [
                'name' => $name,
                'username' => $username,
                'email' => $username,
                'password' => $this->hashedPassword,
                'role' => 'mentor',
                'otp_verified' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ];
            
            $this->mentors[] = [
                'name' => $name,
                'email' => $username,
                'phone' => '08' . rand(1000000000, 9999999999),
            ];
            
            if (count($batch) >= 500) {
                DB::table('users')->insert($batch);
                $batch = [];
            }
        }
        
        if (!empty($batch)) {
            DB::table('users')->insert($batch);
        }
        
        $this->command->info('✅ 2,000 mentors created');
    }
    
    private function createMahasiswaWithProposals()
    {
        $this->command->info('🎓 Creating 40,000 mahasiswa with proposals...');
        
        // Start user ID counter from last ID (after dosen + mentors)
        $this->userIdCounter = DB::table('users')->max('id') + 1;
        
        $totalMhs = 40000;
        $mhsPerDosen = 100;
        $chunkSize = 500;
        
        // Distribution: 40,000 total
        // 12,000 (30%) - no proposal
        // 28,000 (70%) - with proposal
        //   Of 28,000 proposals: 14,000 (50%) graded, 14,000 (50%) not graded
        $noProposal = 12000;
        $proposalOnly = 8000;
        $partialFiles = 6000;
        $graded = 14000;
        
        $mhsBatch = [];
        $proposalBatch = [];
        $counter = 0;
        $proposalCounter = 0;
        
        for ($dosenIdx = 0; $dosenIdx < 400; $dosenIdx++) {
            $dosenNip = $this->dosen[$dosenIdx];
            
            // Each dosen: 50 Magang + 50 MSIB
            for ($mhsNum = 0; $mhsNum < $mhsPerDosen; $mhsNum++) {
                $counter++;
                $isMSIB = ($mhsNum >= 50);
                
                $nim = rand(19, 26) . str_pad($counter, 6, '0', STR_PAD_LEFT);
                $name = $this->randomName();
                
                $userId = $this->userIdCounter++;
                
                $mhsBatch[] = [
                    'id' => $userId,
                    'name' => $name,
                    'username' => $nim,
                    'email' => strtolower(str_replace(' ', '', $name)) . $nim . '@gmail.com',
                    'email_bsi' => $nim . '@bsi.ac.id',
                    'password' => $this->hashedPassword,
                    'role' => 'mahasiswa',
                    'nama_dosen_pa' => $dosenNip,
                    'kd_lokal' => rand(10, 15) . '.' . rand(1, 9) . chr(rand(65, 90)) . '.0' . rand(1, 5),
                    'otp_verified' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
                
                // Determine if has proposal
                $hasProposal = $counter > $noProposal;
                
                if ($hasProposal) {
                    $proposalCounter++;
                    $mentor = $this->mentors[array_rand($this->mentors)];
                    
                    // Determine status
                    if ($proposalCounter <= $graded) {
                        $status = 'graded';
                    } elseif ($proposalCounter <= ($graded + $partialFiles)) {
                        $status = 'partial';
                    } else {
                        $status = 'proposal_only';
                    }
                    
                    $proposalBatch[] = $this->createProposal(
                        $userId,
                        $nim,
                        $name,
                        $isMSIB ? 'MSIB' : 'Magang',
                        $dosenNip,
                        $mentor,
                        $status
                    );
                }
                
                // Insert in chunks
                if (count($mhsBatch) >= $chunkSize) {
                    DB::table('users')->insert($mhsBatch);
                    $mhsBatch = [];
                }
                
                if (count($proposalBatch) >= $chunkSize) {
                    DB::table('proposal_mahasiswas')->insert($proposalBatch);
                    $proposalBatch = [];
                }
                
                if ($counter % 2000 == 0) {
                    $this->command->info("  Progress: {$counter}/{$totalMhs} mahasiswa | {$proposalCounter} proposals");
                }
            }
        }
        
        // Insert remaining
        if (!empty($mhsBatch)) {
            DB::table('users')->insert($mhsBatch);
        }
        if (!empty($proposalBatch)) {
            DB::table('proposal_mahasiswas')->insert($proposalBatch);
        }
        
        $this->command->info('✅ 40,000 mahasiswa created');
        $this->command->info('✅ ' . $proposalCounter . ' proposals created');
    }
    
    private function createProposal($userId, $nim, $nama, $jnsPkl, $dosenPa, $mentor, $status)
    {
        $judul = $this->randomJudul($jnsPkl);
        $tempat = $this->companies[array_rand($this->companies)];
        
        $proposal = [
            'user_id' => $userId,
            'nim' => $nim,
            'nama' => $nama,
            'jns_pkl' => $jnsPkl,
            'judul_pkl' => $judul,
            'tempat_riset' => $tempat,
            'nama_mentor' => $mentor['name'],
            'email_mentor' => $mentor['email'],
            'hp_mentor' => $mentor['phone'],
            'dosen_pa' => $dosenPa,
            'nilai' => 0,
            'penilai' => null,
            'skm' => null,
            'proposal' => null,
            'lp' => null,
            'lpp' => null,
            'skp' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ];
        
        if ($status === 'graded') {
            // Complete files + graded (50% of proposals)
            $proposal['skm'] = 'surat_keterangan_magang_' . $nim . '.pdf';
            $proposal['proposal'] = 'proposal_pkl_' . $nim . '.pdf';
            $proposal['lp'] = 'laporan_pkl_' . $nim . '.pdf';
            $proposal['lpp'] = 'lembar_penilaian_' . $nim . '.pdf';
            $proposal['skp'] = 'surat_keterangan_perusahaan_' . $nim . '.pdf';
            $proposal['nilai'] = rand(70, 95);
            $proposal['penilai'] = $dosenPa;
        } elseif ($status === 'partial') {
            // Random 1-2 files
            $fileCount = rand(1, 2);
            $files = ['skm', 'proposal', 'lp'];
            shuffle($files);
            for ($i = 0; $i < $fileCount; $i++) {
                $fileType = $files[$i];
                $proposal[$fileType] = $fileType . '_' . $nim . '.pdf';
            }
        }
        // else: proposal_only - no files
        
        return $proposal;
    }
    
    private function randomName()
    {
        return $this->namaDepan[array_rand($this->namaDepan)] . ' ' . 
               $this->namaBelakang[array_rand($this->namaBelakang)];
    }
    
    private function randomJudul($jnsPkl)
    {
        $topics = ['Sistem Informasi', 'Website E-Commerce', 'Aplikasi Mobile', 'Dashboard Analytics', 'Platform Digital', 'Sistem Manajemen'];
        $actions = ['Pengembangan', 'Analisis dan Perancangan', 'Implementasi', 'Optimasi', 'Desain dan Implementasi'];
        
        if ($jnsPkl === 'MSIB') {
            return 'Studi Independen Bersertifikat - ' . $topics[array_rand($topics)];
        }
        
        return $actions[array_rand($actions)] . ' ' . $topics[array_rand($topics)] . ' Berbasis Web';
    }
    
    private function showStats()
    {
        $this->command->newLine();
        $this->command->info('📊 Database Statistics:');
        $this->command->info('  - Total Users: ' . DB::table('users')->count());
        $this->command->info('  - Dosen: ' . DB::table('users')->where('role', 'dosen')->count());
        $this->command->info('  - Mentor: ' . DB::table('users')->where('role', 'mentor')->count());
        $this->command->info('  - Mahasiswa: ' . DB::table('users')->where('role', 'mahasiswa')->count());
        $this->command->info('  - Total Proposals: ' . DB::table('proposal_mahasiswas')->count());
        $this->command->info('  - Sudah Dinilai: ' . DB::table('proposal_mahasiswas')->where('nilai', '>', 0)->count());
        $this->command->info('  - Belum Dinilai: ' . DB::table('proposal_mahasiswas')->where('nilai', 0)->count());
    }
}
