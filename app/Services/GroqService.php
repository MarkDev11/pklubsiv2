<?php

namespace App\Services;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GroqService
{
    protected string $apiKey;

    protected string $model;

    protected string $baseUrl = 'https://api.groq.com/openai/v1/chat/completions';

    public function __construct()
    {
        $this->apiKey = config('services.groq.api_key', '');
        $this->model = config('services.groq.model', 'llama-3.3-70b-versatile');
    }

    /**
     * Membuat System Prompt berdasarkan Role User.
     */
    protected function buildSystemPrompt(): string
    {
        $user = Auth::user();

        if (! $user instanceof User) {
            return 'Kamu adalah asisten AI untuk Sistem Informasi Praktik Kerja Lapangan (PKL) Universitas Bina Sarana Informatika (UBSI). Jawab secara umum, informatif, dan dalam Bahasa Indonesia yang ramah.';
        }

        $base = "Kamu adalah Assistant AI cerdas 'PKL-Bot' untuk Sistem Informasi PKL Universitas Bina Sarana Informatika (UBSI). 
ATURAN KERAS: JAWABANMU HARUS SANGAT AKURAT DAN SESUAI DENGAN SPESIFIKASI SISTEM! JANGAN MENGARANG ATURAN ATAU MENU YANG TIDAK ADA! Gunakan sintaks Markdown. Gunakan Bahasa Indonesia yang luwes, jelas, dan profesional.\n\n";

        return match ($user->role) {
            UserRole::Admin => $base."Status lawan bicaramu: Administrator Sistem. 
Tugas spesifikmu: Berikan bantuan teknis terkait pengelolaan menu 'Kelola Akun', pengaturan jadwal di menu 'Kalender Sistem' (pendaftaran, upload laporan, input nilai), eksekusi rekap laporan di menu 'Rekap PDF', dan manajemen pengguna (dosen, mentor, mahasiswa).
Aturan sistem: Admin dapat mereset password, membuka/menutup akses portal sesuai tanggal, dan mengubah Dosen PA untuk mahasiswa.",

            UserRole::Mahasiswa => $base.'Status lawan bicaramu: Mahasiswa BSI ('.$user->name."). 
Tugas spesifikmu: Bimbing mahasiswa mengenai langkah-langkah input data PKL dan Laporan Akhir.
PENGETAHUAN MENU & SISTEM (SANGAT PENTING):
1. Pengajuan Data PKL: Dilakukan di menu 'Input Data PKL'. Form yang harus diisi mahasiswa meliputi: Jenis PKL (Magang Reguler atau MSIB/MBKM), Judul PKL, Nama Instansi/Tempat Riset, Nama Mentor Industri, HP Mentor, Email Mentor, dan mengunggah 'Surat Keterangan Magang (SKM)' (harus PDF max 40MB).
2. Upload Laporan Akhir: Hanya bisa dilakukan setelah Data PKL terinput. Dilakukan di menu tombol panah / 'Upload Laporan'. Ada 3 dokumen wajib: Laporan Akhir PKL (PDF), Lembar Penilaian Perusahaan (cap & ttd), dan Surat Keterangan Selesai Magang.
3. Ingatkan mahasiswa bahwa masa pengisian form diatur oleh kalender admin. Jika waktu lewat, fungsi akan terkunci.
Jawablah dengan langkah-langkah yang TEPAT merujuk pada form-form di atas, jangan berikan instruksi umum abal-abal. Ramah dan memotivasi.",

            UserRole::Dosen => $base."Status lawan bicaramu: Dosen Penasihat Akademik (Dosen PA). 
Tugas spesifikmu: Pandu dosen untuk mengecek menu 'Data Mahasiswa' (untuk memonitor mahasiswa yang sudah/belum input), dan menu 'Nilai Laporan' (untuk memberi nilai akhir laporan PDF dari 0-100). Dosen PA bisa melihat file PDF mahasiswa dengan mengeklik icon PDF di tabel nilai. Gunakan sapaan yang hormat dan profesional.",

            UserRole::Mentor => $base."Status lawan bicaramu: Mentor Industri / Pembimbing Instansi. 
Tugas spesifikmu: Fasilitasi mentor perusahaan tentang cara mengisi form evaluasi dengan menekan tombol 'Beri Nilai' di Dashboard mereka. Parameter penilaian UBSI mencakup poin teknis dan non-teknis. Nilai otomatis mempengaruhi kelulusan mahasiswa. Gunakan bahasa profesional bisnis yang menunjukkan apresiasi.",
        };
    }

    /**
     * Kirim pesan ke Groq AI dan dapatkan respons.
     *
     * @param  array<int, array<string, string>>  $history
     */
    public function chat(string $message, array $history = []): ?string
    {
        if (empty($this->apiKey)) {
            return 'API Key Groq belum dikonfigurasi. Silakan hubungi administrator.';
        }

        $messages = [
            [
                'role' => 'system',
                'content' => $this->buildSystemPrompt(),
            ],
        ];

        foreach ($history as $msg) {
            $messages[] = $msg;
        }

        $messages[] = ['role' => 'user', 'content' => $message];

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer '.$this->apiKey,
                'Content-Type' => 'application/json',
            ])->timeout(30)->post($this->baseUrl, [
                'model' => $this->model,
                'messages' => $messages,
                'max_tokens' => 1024,
                'temperature' => 0.7,
            ]);

            if ($response->successful()) {
                return $response->json('choices.0.message.content');
            }

            Log::error('Groq API Error', ['status' => $response->status(), 'body' => $response->body()]);

            return 'Maaf, terjadi kesalahan saat menghubungi AI. Silakan coba lagi.';
        } catch (\Exception $e) {
            Log::error('Groq API Exception', ['message' => $e->getMessage()]);

            return 'Maaf, layanan AI sedang tidak tersedia. Silakan coba lagi nanti.';
        }
    }
}
