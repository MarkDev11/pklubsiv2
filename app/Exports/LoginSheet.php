<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class LoginSheet implements FromArray, WithHeadings, WithTitle, WithStyles
{
    public function array(): array
    {
        return [
            // Login Standard
            [1, 'Login Standard', 'Login mahasiswa dengan username, password, dan role yang benar', 'Berhasil login dan redirect ke dashboard mahasiswa', 'Sesuai harapan', 'Berhasil'],
            [2, 'Login Standard', 'Login dosen dengan NIP, password, dan role yang benar', 'Berhasil login dan redirect ke dashboard dosen', 'Sesuai harapan', 'Berhasil'],
            [3, 'Login Standard', 'Login mentor dengan email, password, dan role yang benar', 'Berhasil login dan redirect ke dashboard mentor', 'Sesuai harapan', 'Berhasil'],
            [4, 'Login Standard', 'Login admin dengan username, password, dan role yang benar', 'Berhasil login dan redirect ke dashboard admin', 'Sesuai harapan', 'Berhasil'],
            [5, 'Login Standard', 'Login dengan username yang salah', 'Muncul error "Kredensial tidak valid"', 'Sesuai harapan', 'Berhasil'],
            [6, 'Login Standard', 'Login dengan password yang salah', 'Muncul error "Kredensial tidak valid"', 'Sesuai harapan', 'Berhasil'],
            [7, 'Login Standard', 'Login dengan role yang tidak sesuai data user', 'Muncul error "Role tidak sesuai"', 'Sesuai harapan', 'Berhasil'],
            [8, 'Login Standard', 'Login tanpa verifikasi captcha', 'Muncul error validasi captcha', 'Sesuai harapan', 'Berhasil'],
            [9, 'Login Standard', 'Login gagal 6 kali berturut-turut (rate limiting)', 'Akun di-throttle, muncul error "Too many attempts"', 'Sesuai harapan', 'Berhasil'],
            [10, 'Login Standard', 'Login dengan remember me dicentang', 'Berhasil login dan session bertahan lebih lama', 'Sesuai harapan', 'Berhasil'],
            
            // Google OAuth
            [11, 'Google OAuth', 'Login Google dengan email @bsi.ac.id yang terdaftar', 'Berhasil login dan redirect ke dashboard tanpa OTP', 'Sesuai harapan', 'Berhasil'],
            [12, 'Google OAuth', 'Login Google dengan email bukan @bsi.ac.id', 'Muncul error "Email harus menggunakan domain @bsi.ac.id"', 'Sesuai harapan', 'Berhasil'],
            [13, 'Google OAuth', 'Login Google dengan email @bsi.ac.id belum terdaftar', 'Muncul error "Email tidak terdaftar"', 'Sesuai harapan', 'Berhasil'],
            [14, 'Google OAuth', 'Login Google pertama kali (google_id belum ada)', 'System update google_id dan berhasil login', 'Sesuai harapan', 'Berhasil'],
            [15, 'Google OAuth', 'Google authentication gagal di sisi Google', 'Redirect ke login dengan error message', 'Sesuai harapan', 'Berhasil'],
            
            // OTP Verification
            [16, 'OTP Verification', 'Verifikasi OTP dengan kode yang benar', 'Berhasil verifikasi dan redirect ke dashboard', 'Sesuai harapan', 'Berhasil'],
            [17, 'OTP Verification', 'Verifikasi OTP dengan kode yang salah', 'Muncul error "Kode OTP salah"', 'Sesuai harapan', 'Berhasil'],
            [18, 'OTP Verification', 'Verifikasi OTP yang sudah expired (>5 menit)', 'Muncul error "Kode OTP sudah kadaluarsa"', 'Sesuai harapan', 'Berhasil'],
            [19, 'OTP Verification', 'Resend OTP berhasil', 'OTP baru digenerate dan dikirim ke email', 'Sesuai harapan', 'Berhasil'],
            [20, 'OTP Verification', 'Resend OTP sebelum cooldown 60 detik habis', 'Muncul error "Tunggu 60 detik untuk resend"', 'Sesuai harapan', 'Berhasil'],
            [21, 'OTP Verification', 'Verifikasi OTP salah lebih dari 5 kali', 'Rate limiting aktif, akses diblokir sementara', 'Sesuai harapan', 'Berhasil'],
            [22, 'OTP Verification', 'Akses route lain tanpa verifikasi OTP', 'Redirect ke halaman OTP verification', 'Sesuai harapan', 'Berhasil'],
            [23, 'OTP Verification', 'OTP auto-generate saat load halaman', 'OTP baru dibuat jika belum ada atau expired', 'Sesuai harapan', 'Berhasil'],
            
            // Forgot Password
            [24, 'Forgot Password', 'Request reset password dengan email terdaftar', 'OTP dikirim ke email dan redirect ke verifikasi', 'Sesuai harapan', 'Berhasil'],
            [25, 'Forgot Password', 'Request reset password dengan email tidak terdaftar', 'Muncul error "Email tidak ditemukan"', 'Sesuai harapan', 'Berhasil'],
            [26, 'Forgot Password', 'Verifikasi OTP reset password dengan kode benar', 'Berhasil verifikasi dan redirect ke form reset password', 'Sesuai harapan', 'Berhasil'],
            [27, 'Forgot Password', 'Verifikasi OTP reset password dengan kode salah', 'Muncul error "Kode OTP salah"', 'Sesuai harapan', 'Berhasil'],
            [28, 'Forgot Password', 'Verifikasi OTP reset yang sudah expired', 'Muncul error "Kode OTP kadaluarsa"', 'Sesuai harapan', 'Berhasil'],
            [29, 'Forgot Password', 'Resend OTP reset password berhasil', 'OTP baru dikirim, counter resend bertambah', 'Sesuai harapan', 'Berhasil'],
            [30, 'Forgot Password', 'Resend OTP lebih dari 3 kali', 'Muncul error "Maksimal 3 kali resend"', 'Sesuai harapan', 'Berhasil'],
            [31, 'Forgot Password', 'Reset password dengan password baru valid', 'Password berhasil diupdate, redirect ke login', 'Sesuai harapan', 'Berhasil'],
            [32, 'Forgot Password', 'Reset password dengan password kurang dari 8 karakter', 'Muncul error validasi "Minimal 8 karakter"', 'Sesuai harapan', 'Berhasil'],
            [33, 'Forgot Password', 'Reset password dengan konfirmasi tidak cocok', 'Muncul error "Konfirmasi password tidak cocok"', 'Sesuai harapan', 'Berhasil'],
            
            // Logout
            [34, 'Logout', 'Logout dari sistem', 'Session dihapus dan redirect ke halaman login', 'Sesuai harapan', 'Berhasil'],
            [35, 'Logout', 'Akses route protected setelah logout', 'Redirect ke login page', 'Sesuai harapan', 'Berhasil'],
        ];
    }

    public function headings(): array
    {
        return [
            'No',
            'Skenario Pengujian',
            'Test Case',
            'Hasil Yang Diharapkan',
            'Hasil Pengujian',
            'Kesimpulan'
        ];
    }

    public function title(): string
    {
        return 'Login & Autentikasi';
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}
