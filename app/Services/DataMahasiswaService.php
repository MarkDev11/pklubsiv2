<?php

namespace App\Services;

use App\Enums\UserRole;
use App\Models\ProposalMahasiswa;
use App\Models\User;

class DataMahasiswaService
{
    /**
     * @return array{sedangBerjalan: mixed, laporanTuntas: mixed, belumInput: mixed}
     */
    public function getGroupedForDosen(User $dosen, string $jenis): array
    {
        $scopeMethod = $jenis === 'Magang' ? 'magang' : 'msib';
        $jenisFilter = $jenis === 'Magang'
            ? 'Magang'
            : 'Program Magang khusus (PMK/GNIK/MBKM/MSIB/PMMB)';

        $sedangBerjalan = ProposalMahasiswa::$scopeMethod()
            ->byDosen($dosen->name)
            ->where(fn ($q) => $q->whereNull('lp')->orWhereNull('lpp')->orWhereNull('skp'))
            ->with('user')->paginate(50, ['*'], 'berjalan');

        $laporanTuntas = ProposalMahasiswa::$scopeMethod()
            ->byDosen($dosen->name)
            ->whereNotNull('lp')->whereNotNull('lpp')->whereNotNull('skp')
            ->with('user')->paginate(50, ['*'], 'tuntas');

        $belumInput = User::where('role', UserRole::Mahasiswa)
            ->where('nama_dosen_pa', $dosen->name)
            ->where('jenis', $jenisFilter)
            ->whereDoesntHave('proposalMahasiswa')
            ->paginate(50, ['*'], 'belum');

        return compact('sedangBerjalan', 'laporanTuntas', 'belumInput');
    }

    /**
     * @return array{sedangBerjalan: mixed, telahDinilai: mixed, perluNilai: mixed}
     */
    public function getGroupedForMentor(User $mentor, string $jenis): array
    {
        $scopeMethod = $jenis === 'Magang' ? 'magang' : 'msib';

        $sedangBerjalan = ProposalMahasiswa::$scopeMethod()
            ->where('email_mentor', $mentor->username)
            ->where(fn ($q) => $q->whereNull('lp')->orWhereNull('lpp')->orWhereNull('skp'))
            ->where(fn ($q) => $q->whereNull('nilai')->orWhere('nilai', 0))
            ->with('user')->paginate(50, ['*'], 'berjalan');

        $telahDinilai = ProposalMahasiswa::$scopeMethod()
            ->where('email_mentor', $mentor->username)
            ->where('nilai', '>', 0)
            ->with('user')->paginate(50, ['*'], 'dinilai');

        $perluNilai = ProposalMahasiswa::$scopeMethod()
            ->where('email_mentor', $mentor->username)
            ->whereNotNull('lp')->whereNotNull('lpp')->whereNotNull('skp')
            ->where(fn ($q) => $q->whereNull('nilai')->orWhere('nilai', 0))
            ->with('user')->paginate(50, ['*'], 'perlu');

        return compact('sedangBerjalan', 'telahDinilai', 'perluNilai');
    }
}
