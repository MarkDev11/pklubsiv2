<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class DosenMentorSheet implements FromArray, WithHeadings, WithTitle, WithStyles
{
    public function array(): array
    {
        return [
            [1, 'Dashboard Dosen', 'Tampil statistik bimbingan (total, sudah dinilai, belum dinilai)', 'Dashboard dosen menampilkan 4 card statistik', 'Sesuai harapan', 'Berhasil'],
            [2, 'Dashboard Dosen', 'Tampil timeline akademik lengkap', 'Timeline menampilkan ketiga fase dengan tanggal', 'Sesuai harapan', 'Berhasil'],
            [3, 'Dashboard Dosen', 'Tampil modal daftar mahasiswa bimbingan', 'Modal muncul menampilkan list mahasiswa PA', 'Sesuai harapan', 'Berhasil'],
            [4, 'Dashboard Dosen', 'Quick action navigate ke PKL/MSIB', 'Link mengarah ke halaman yang benar', 'Sesuai harapan', 'Berhasil'],
            [5, 'Dashboard Mentor', 'Tampil statistik bimbingan mentor', 'Dashboard mentor menampilkan 3 card statistik', 'Sesuai harapan', 'Berhasil'],
            [6, 'Dashboard Mentor', 'Tampil deadline alert dengan countdown', 'Alert menampilkan sisa hari deadline', 'Sesuai harapan', 'Berhasil'],
            [7, 'Dashboard Mentor', 'Quick navigation ke PKL/MSIB management', 'Card link mengarah ke halaman yang benar', 'Sesuai harapan', 'Berhasil'],
            [8, 'Mahasiswa Data Dosen', 'Lihat mahasiswa PKL yang dibimbing', 'Tabel menampilkan mahasiswa PKL sesuai NIP Dosen PA', 'Sesuai harapan', 'Berhasil'],
            [9, 'Mahasiswa Data Dosen', 'Lihat mahasiswa MSIB yang dibimbing', 'Tabel menampilkan mahasiswa MSIB sesuai NIP Dosen PA', 'Sesuai harapan', 'Berhasil'],
            [10, 'Mahasiswa Data Dosen', 'Filter Sedang Berjalan', 'Tampil mahasiswa dengan dokumen tidak lengkap', 'Sesuai harapan', 'Berhasil'],
            [11, 'Mahasiswa Data Dosen', 'Filter Laporan Tuntas', 'Tampil mahasiswa dengan semua dokumen lengkap', 'Sesuai harapan', 'Berhasil'],
            [12, 'Mahasiswa Data Dosen', 'Filter Belum Input Data', 'Tampil mahasiswa yang belum submit proposal', 'Sesuai harapan', 'Berhasil'],
            [13, 'Mahasiswa Data Dosen', 'Search mahasiswa by nama/NIM', 'Tabel filter sesuai keyword pencarian', 'Sesuai harapan', 'Berhasil'],
            [14, 'Mahasiswa Data Dosen', 'Lihat detail mahasiswa via modal AJAX', 'Modal muncul menampilkan data lengkap mahasiswa', 'Sesuai harapan', 'Berhasil'],
            [15, 'Mahasiswa Data Dosen', 'Download dokumen mahasiswa bimbingan', 'File dokumen berhasil didownload', 'Sesuai harapan', 'Berhasil'],
            [16, 'Mahasiswa Data Mentor', 'Lihat mahasiswa PKL yang dibimbing', 'Tabel menampilkan mahasiswa sesuai email_mentor', 'Sesuai harapan', 'Berhasil'],
            [17, 'Mahasiswa Data Mentor', 'Lihat mahasiswa MSIB yang dibimbing', 'Tabel menampilkan mahasiswa MSIB sesuai email_mentor', 'Sesuai harapan', 'Berhasil'],
            [18, 'Mahasiswa Data Mentor', 'Tab Sedang Berjalan', 'Tampil mahasiswa dengan dokumen incomplete dan belum dinilai', 'Sesuai harapan', 'Berhasil'],
            [19, 'Mahasiswa Data Mentor', 'Tab Perlu Nilai', 'Tampil mahasiswa dengan dokumen lengkap menunggu nilai', 'Sesuai harapan', 'Berhasil'],
            [20, 'Mahasiswa Data Mentor', 'Tab Telah Dinilai', 'Tampil mahasiswa yang sudah diberi nilai', 'Sesuai harapan', 'Berhasil'],
            [21, 'Mahasiswa Data Mentor', 'Search mahasiswa by nama/NIM', 'Tabel filter sesuai keyword pencarian', 'Sesuai harapan', 'Berhasil'],
            [22, 'Mahasiswa Data Mentor', 'Lihat detail mahasiswa via modal', 'Modal muncul menampilkan data lengkap mahasiswa', 'Sesuai harapan', 'Berhasil'],
            [23, 'Grading Dosen', 'Input nilai PKL berhasil (75-100)', 'Nilai tersimpan dan email notifikasi terkirim', 'Sesuai harapan', 'Berhasil'],
            [24, 'Grading Dosen', 'Input nilai MSIB berhasil', 'Nilai tersimpan dan auto-save berfungsi', 'Sesuai harapan', 'Berhasil'],
            [25, 'Grading Dosen', 'Auto-save nilai on blur', 'Nilai otomatis tersimpan saat keluar dari input', 'Sesuai harapan', 'Berhasil'],
            [26, 'Grading Dosen', 'Download dokumen LP/LPP/SKP dari tabel', 'File berhasil didownload dari badge dokumen', 'Sesuai harapan', 'Berhasil'],
            [27, 'Grading Dosen', 'Filter by kelengkapan dokumen', 'Tabel filter sesuai status kelengkapan', 'Sesuai harapan', 'Berhasil'],
            [28, 'Grading Dosen', 'Input nilai di luar periode grading', 'Input disabled dengan warning periode tutup', 'Sesuai harapan', 'Berhasil'],
            [29, 'Grading Mentor', 'Input nilai PKL mentor berhasil', 'Nilai mentor tersimpan di database', 'Sesuai harapan', 'Berhasil'],
            [30, 'Grading Mentor', 'Input nilai MSIB mentor berhasil', 'Nilai mentor tersimpan dan auto-save berfungsi', 'Sesuai harapan', 'Berhasil'],
            [31, 'Grading Mentor', 'Auto-save nilai mentor', 'Nilai otomatis tersimpan saat blur', 'Sesuai harapan', 'Berhasil'],
            [32, 'Grading Mentor', 'Kombinasi nilai: rata-rata Dosen+Mentor', 'Nilai akhir dihitung rata-rata jika keduanya >= 75', 'Sesuai harapan', 'Berhasil'],
            [33, 'Reports', 'Generate PDF nilai PKL per Dosen/Mentor', 'File PDF rekap nilai berhasil dibuat', 'Sesuai harapan', 'Berhasil'],
            [34, 'Reports', 'Generate PDF nilai MSIB per Dosen/Mentor', 'File PDF rekap MSIB berhasil dibuat', 'Sesuai harapan', 'Berhasil'],
            [35, 'Profile', 'Ganti password dosen/mentor', 'Password terupdate dan bisa login dengan password baru', 'Sesuai harapan', 'Berhasil'],
        ];
    }

    public function headings(): array
    {
        return ['No', 'Skenario Pengujian', 'Test Case', 'Hasil Yang Diharapkan', 'Hasil Pengujian', 'Kesimpulan'];
    }

    public function title(): string
    {
        return 'Full Function Dosen-Mentor';
    }

    public function styles(Worksheet $sheet)
    {
        return [1 => ['font' => ['bold' => true]]];
    }
}
