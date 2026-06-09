<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class AdminSheet implements FromArray, WithHeadings, WithTitle, WithStyles
{
    public function array(): array
    {
        return [
            [1, 'Dashboard', 'Tampil statistik total user (mahasiswa, dosen, mentor, admin)', 'Dashboard menampilkan 4 card statistik dengan angka yang benar', 'Sesuai harapan', 'Berhasil'],
            [2, 'Dashboard', 'Tampil progress input PKL dengan persentase', 'Progress bar menampilkan persentase mahasiswa yang sudah input', 'Sesuai harapan', 'Berhasil'],
            [3, 'Dashboard', 'Tampil recent activity logs (8 terakhir)', 'Tabel activity logs menampilkan 8 log terbaru', 'Sesuai harapan', 'Berhasil'],
            [4, 'User Management', 'Lihat daftar semua user dengan DataTables', 'Tabel user tampil dengan pagination dan data lengkap', 'Sesuai harapan', 'Berhasil'],
            [5, 'User Management', 'Search user by username/nama/email', 'DataTables filter user sesuai keyword pencarian', 'Sesuai harapan', 'Berhasil'],
            [6, 'User Management', 'Filter user by role mahasiswa', 'Hanya menampilkan user dengan role mahasiswa', 'Sesuai harapan', 'Berhasil'],
            [7, 'User Management', 'Filter user by role dosen', 'Hanya menampilkan user dengan role dosen', 'Sesuai harapan', 'Berhasil'],
            [8, 'User Management', 'Filter user by role mentor', 'Hanya menampilkan user dengan role mentor', 'Sesuai harapan', 'Berhasil'],
            [9, 'User Management', 'Filter user by role admin', 'Hanya menampilkan user dengan role admin', 'Sesuai harapan', 'Berhasil'],
            [10, 'User Management', 'Tambah user mahasiswa baru', 'User berhasil dibuat dan muncul di daftar', 'Sesuai harapan', 'Berhasil'],
            [11, 'User Management', 'Tambah user dengan username yang sudah ada', 'Muncul error validasi username duplikat', 'Sesuai harapan', 'Berhasil'],
            [12, 'User Management', 'Edit data user berhasil', 'Data user terupdate di database dan tabel', 'Sesuai harapan', 'Berhasil'],
            [13, 'User Management', 'Edit user ganti username ke yang sudah ada', 'Muncul error validasi username duplikat', 'Sesuai harapan', 'Berhasil'],
            [14, 'User Management', 'Reset password user', 'Password user direset dan email notifikasi terkirim', 'Sesuai harapan', 'Berhasil'],
            [15, 'User Management', 'Hapus user non-admin berhasil', 'User terhapus dari database dan tabel', 'Sesuai harapan', 'Berhasil'],
            [16, 'User Management', 'Hapus user admin (tidak diizinkan)', 'Muncul error tidak bisa menghapus admin', 'Sesuai harapan', 'Berhasil'],
            [17, 'User Management', 'Lihat activity log per user', 'Halaman activity log menampilkan riwayat aktivitas user', 'Sesuai harapan', 'Berhasil'],
            [18, 'User Management', 'DataTables pagination berfungsi', 'Bisa navigasi antar halaman dengan benar', 'Sesuai harapan', 'Berhasil'],
            [19, 'Import Data', 'Import Excel dengan format benar', 'User berhasil diimport dan muncul di daftar', 'Sesuai harapan', 'Berhasil'],
            [20, 'Import Data', 'Import file bukan Excel', 'Muncul error validasi tipe file', 'Sesuai harapan', 'Berhasil'],
            [21, 'Import Data', 'Import file lebih dari 5MB', 'Muncul error validasi ukuran file', 'Sesuai harapan', 'Berhasil'],
            [22, 'Import Data', 'Import dengan username duplikat', 'Username duplikat di-skip, yang lain berhasil', 'Sesuai harapan', 'Berhasil'],
            [23, 'Import Data', 'Download template import', 'File template Excel berhasil didownload', 'Sesuai harapan', 'Berhasil'],
            [24, 'Mahasiswa Board', 'Tampil daftar mahasiswa dengan status', 'Tabel mahasiswa tampil dengan status lengkap', 'Sesuai harapan', 'Berhasil'],
            [25, 'Mahasiswa Board', 'Filter mahasiswa Belum Input', 'Hanya tampil mahasiswa tanpa proposal', 'Sesuai harapan', 'Berhasil'],
            [26, 'Mahasiswa Board', 'Filter mahasiswa Sedang Proses', 'Hanya tampil mahasiswa dengan dokumen tidak lengkap', 'Sesuai harapan', 'Berhasil'],
            [27, 'Mahasiswa Board', 'Filter mahasiswa Dokumen Komplit', 'Hanya tampil mahasiswa dengan semua dokumen lengkap', 'Sesuai harapan', 'Berhasil'],
            [28, 'Mahasiswa Board', 'Search mahasiswa by nama/NIM', 'DataTables filter mahasiswa sesuai keyword', 'Sesuai harapan', 'Berhasil'],
            [29, 'Mahasiswa Board', 'Export PDF daftar mahasiswa', 'File PDF berhasil digenerate dan didownload', 'Sesuai harapan', 'Berhasil'],
            [30, 'Grading', 'Tampil daftar proposal PKL untuk dinilai', 'Tabel proposal PKL tampil dengan data lengkap', 'Sesuai harapan', 'Berhasil'],
            [31, 'Grading', 'Tampil daftar proposal MSIB untuk dinilai', 'Tabel proposal MSIB tampil dengan data lengkap', 'Sesuai harapan', 'Berhasil'],
            [32, 'Grading', 'Input nilai berhasil (75-100)', 'Nilai tersimpan dan auto-save berfungsi', 'Sesuai harapan', 'Berhasil'],
            [33, 'Grading', 'Input nilai kurang dari 75', 'Muncul error validasi nilai minimal 75', 'Sesuai harapan', 'Berhasil'],
            [34, 'Grading', 'Input nilai lebih dari 100', 'Muncul error validasi nilai maksimal 100', 'Sesuai harapan', 'Berhasil'],
            [35, 'Grading', 'Auto-save nilai saat blur input', 'Nilai tersimpan otomatis tanpa klik tombol', 'Sesuai harapan', 'Berhasil'],
            [36, 'Grading', 'Download dokumen LP dari tabel', 'File LP berhasil didownload', 'Sesuai harapan', 'Berhasil'],
            [37, 'Grading', 'Download dokumen LPP dari tabel', 'File LPP berhasil didownload', 'Sesuai harapan', 'Berhasil'],
            [38, 'Grading', 'Download dokumen SKP dari tabel', 'File SKP berhasil didownload', 'Sesuai harapan', 'Berhasil'],
            [39, 'Grading', 'Search mahasiswa di halaman nilai', 'Tabel filter sesuai keyword pencarian', 'Sesuai harapan', 'Berhasil'],
            [40, 'Grading', 'Filter by kelengkapan dokumen', 'Tabel filter sesuai status kelengkapan', 'Sesuai harapan', 'Berhasil'],
            [41, 'Grading', 'Filter by status nilai', 'Tabel filter sesuai status penilaian', 'Sesuai harapan', 'Berhasil'],
            [42, 'Timeline Settings', 'Edit timeline pendaftaran berhasil', 'Timeline pendaftaran terupdate di database', 'Sesuai harapan', 'Berhasil'],
            [43, 'Timeline Settings', 'Edit timeline upload laporan berhasil', 'Timeline upload laporan terupdate di database', 'Sesuai harapan', 'Berhasil'],
            [44, 'Timeline Settings', 'Edit timeline penilaian berhasil', 'Timeline penilaian terupdate di database', 'Sesuai harapan', 'Berhasil'],
            [45, 'Timeline Settings', 'Validasi close_time sebelum open_time', 'Muncul error validasi tanggal tidak valid', 'Sesuai harapan', 'Berhasil'],
            [46, 'Reports', 'Generate PDF nilai PKL', 'File PDF rekap nilai PKL berhasil dibuat', 'Sesuai harapan', 'Berhasil'],
            [47, 'Reports', 'Generate PDF nilai MSIB', 'File PDF rekap nilai MSIB berhasil dibuat', 'Sesuai harapan', 'Berhasil'],
            [48, 'Reports', 'Tampil statistik di dashboard', 'Card statistik menampilkan data real-time', 'Sesuai harapan', 'Berhasil'],
            [49, 'Error Logs', 'Lihat daftar error logs', 'Tabel error logs tampil dengan pagination', 'Sesuai harapan', 'Berhasil'],
            [50, 'Error Logs', 'Hapus single error log', 'Error log terhapus dari database', 'Sesuai harapan', 'Berhasil'],
            [51, 'Error Logs', 'Clear all error logs', 'Semua error logs terhapus setelah konfirmasi', 'Sesuai harapan', 'Berhasil'],
            [52, 'Error Logs', 'Pagination error logs', 'Navigasi antar halaman error logs berfungsi', 'Sesuai harapan', 'Berhasil'],
        ];
    }

    public function headings(): array
    {
        return ['No', 'Skenario Pengujian', 'Test Case', 'Hasil Yang Diharapkan', 'Hasil Pengujian', 'Kesimpulan'];
    }

    public function title(): string
    {
        return 'Full Function Admin';
    }

    public function styles(Worksheet $sheet)
    {
        return [1 => ['font' => ['bold' => true]]];
    }
}
