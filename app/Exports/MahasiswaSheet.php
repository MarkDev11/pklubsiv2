<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class MahasiswaSheet implements FromArray, WithHeadings, WithTitle, WithStyles
{
    public function array(): array
    {
        return [
            [1, 'Dashboard', 'Tampil dashboard dengan statistik lengkap', 'Dashboard menampilkan status SKM, dokumen, dan nilai', 'Sesuai harapan', 'Berhasil'],
            [2, 'Dashboard', 'Tampil empty state jika belum ada proposal', 'Muncul pesan belum ada data PKL dan tombol buat proposal', 'Sesuai harapan', 'Berhasil'],
            [3, 'Dashboard', 'Tampil timeline pendaftaran dan deadline', 'Card timeline menampilkan periode dan status', 'Sesuai harapan', 'Berhasil'],
            [4, 'Dashboard', 'Notifikasi dokumen belum lengkap', 'Badge notifikasi muncul jika dokumen kurang', 'Sesuai harapan', 'Berhasil'],
            [5, 'Proposal Management', 'Buat proposal baru dalam periode pendaftaran', 'Proposal berhasil dibuat dan tersimpan', 'Sesuai harapan', 'Berhasil'],
            [6, 'Proposal Management', 'Buat proposal saat periode ditutup', 'Form disabled dan muncul warning periode tutup', 'Sesuai harapan', 'Berhasil'],
            [7, 'Proposal Management', 'Buat proposal dengan field required kosong', 'Muncul error validasi field wajib diisi', 'Sesuai harapan', 'Berhasil'],
            [8, 'Proposal Management', 'Buat proposal duplikat (sudah punya)', 'Muncul error sudah memiliki proposal', 'Sesuai harapan', 'Berhasil'],
            [9, 'Proposal Management', 'Upload SKM berhasil (PDF kurang dari 40MB)', 'File SKM berhasil diupload dan tersimpan', 'Sesuai harapan', 'Berhasil'],
            [10, 'Proposal Management', 'Upload SKM file bukan PDF', 'Muncul error validasi tipe file harus PDF', 'Sesuai harapan', 'Berhasil'],
            [11, 'Proposal Management', 'Upload SKM file lebih dari 40MB', 'Muncul error validasi ukuran file maksimal 40MB', 'Sesuai harapan', 'Berhasil'],
            [12, 'Proposal Management', 'Input nomor HP dengan format salah', 'Muncul error validasi format nomor HP', 'Sesuai harapan', 'Berhasil'],
            [13, 'Proposal Management', 'Input nomor HP auto-format (tanpa 0 di depan)', 'Nomor HP otomatis ditambah 0 di depan', 'Sesuai harapan', 'Berhasil'],
            [14, 'Proposal Management', 'Input email mentor dengan format salah', 'Muncul error validasi format email', 'Sesuai harapan', 'Berhasil'],
            [15, 'Proposal Management', 'Edit proposal berhasil (belum dinilai)', 'Data proposal terupdate di database', 'Sesuai harapan', 'Berhasil'],
            [16, 'Proposal Management', 'Edit proposal yang sudah dinilai', 'Lock screen muncul, tidak bisa edit', 'Sesuai harapan', 'Berhasil'],
            [17, 'Proposal Management', 'Edit proposal ganti SKM file baru', 'File SKM lama terhapus, file baru tersimpan', 'Sesuai harapan', 'Berhasil'],
            [18, 'Proposal Management', 'Lihat preview PDF SKM sebelum submit', 'Modal preview PDF muncul dan menampilkan file', 'Sesuai harapan', 'Berhasil'],
            [19, 'Proposal Management', 'Lock screen saat sudah dinilai', 'Halaman proposal menampilkan lock screen', 'Sesuai harapan', 'Berhasil'],
            [20, 'Proposal Management', 'Lock screen saat periode tutup', 'Form disabled dengan warning periode tutup', 'Sesuai harapan', 'Berhasil'],
            [21, 'Proposal Management', 'Pilih jenis PKL Magang', 'Jenis PKL tersimpan sebagai Magang', 'Sesuai harapan', 'Berhasil'],
            [22, 'Proposal Management', 'Pilih jenis PKL MSIB/PMK', 'Jenis PKL tersimpan sebagai Program Magang Khusus', 'Sesuai harapan', 'Berhasil'],
            [23, 'Report Management', 'Upload LP berhasil', 'File LP berhasil diupload dan tersimpan', 'Sesuai harapan', 'Berhasil'],
            [24, 'Report Management', 'Upload LPP berhasil', 'File LPP berhasil diupload dan tersimpan', 'Sesuai harapan', 'Berhasil'],
            [25, 'Report Management', 'Upload SKP berhasil', 'File SKP berhasil diupload dan tersimpan', 'Sesuai harapan', 'Berhasil'],
            [26, 'Report Management', 'Upload semua dokumen sekaligus (LP+LPP+SKP)', 'Ketiga file berhasil diupload bersamaan', 'Sesuai harapan', 'Berhasil'],
            [27, 'Report Management', 'Upload partial (hanya 1 atau 2 dokumen)', 'File yang diupload tersimpan, yang lain tetap kosong', 'Sesuai harapan', 'Berhasil'],
            [28, 'Report Management', 'Upload laporan file bukan PDF', 'Muncul error validasi tipe file harus PDF', 'Sesuai harapan', 'Berhasil'],
            [29, 'Report Management', 'Upload laporan file lebih dari 40MB', 'Muncul error validasi ukuran maksimal 40MB', 'Sesuai harapan', 'Berhasil'],
            [30, 'Report Management', 'Replace dokumen yang sudah diupload', 'File lama terhapus, file baru tersimpan', 'Sesuai harapan', 'Berhasil'],
            [31, 'Report Management', 'Reset semua dokumen berhasil', 'Ketiga dokumen (LP, LPP, SKP) terhapus', 'Sesuai harapan', 'Berhasil'],
            [32, 'Report Management', 'Reset dokumen dengan konfirmasi cancel', 'Dokumen tidak terhapus, tetap tersimpan', 'Sesuai harapan', 'Berhasil'],
            [33, 'Report Management', 'Lihat preview PDF laporan sebelum upload', 'Modal preview muncul menampilkan file', 'Sesuai harapan', 'Berhasil'],
            [34, 'Report Management', 'Lock upload saat sudah dinilai', 'Lock screen muncul, upload tidak bisa dilakukan', 'Sesuai harapan', 'Berhasil'],
            [35, 'Profile & Password', 'Lihat profil mahasiswa', 'Halaman profil menampilkan data lengkap', 'Sesuai harapan', 'Berhasil'],
            [36, 'Profile & Password', 'Ganti password berhasil', 'Password terupdate dan bisa login dengan password baru', 'Sesuai harapan', 'Berhasil'],
            [37, 'Profile & Password', 'Ganti password dengan password lama salah', 'Muncul error password lama tidak sesuai', 'Sesuai harapan', 'Berhasil'],
            [38, 'Profile & Password', 'Ganti password dengan konfirmasi tidak cocok', 'Muncul error konfirmasi password tidak cocok', 'Sesuai harapan', 'Berhasil'],
            [39, 'File Access', 'Download dokumen sendiri berhasil', 'File berhasil didownload dengan nama yang benar', 'Sesuai harapan', 'Berhasil'],
            [40, 'File Access', 'Akses dokumen mahasiswa lain', 'Muncul error 403 Forbidden', 'Sesuai harapan', 'Berhasil'],
        ];
    }

    public function headings(): array
    {
        return ['No', 'Skenario Pengujian', 'Test Case', 'Hasil Yang Diharapkan', 'Hasil Pengujian', 'Kesimpulan'];
    }

    public function title(): string
    {
        return 'Full Function Mahasiswa';
    }

    public function styles(Worksheet $sheet)
    {
        return [1 => ['font' => ['bold' => true]]];
    }
}
