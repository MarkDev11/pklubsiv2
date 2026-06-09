<?php

namespace App\Http\Controllers\Mentor;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\DataMahasiswaService;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;

class DataMahasiswaController extends Controller
{
    public function __construct(
        protected DataMahasiswaService $dataService
    ) {}

    public function pklIndex(): View
    {
        $data = $this->dataService->getGroupedForMentor($this->authenticatedUser(), 'Magang');

        return view('mentor.data-mahasiswa-pkl', $data);
    }

    public function msibIndex(): View
    {
        $data = $this->dataService->getGroupedForMentor($this->authenticatedUser(), 'Msib');

        return view('mentor.data-mahasiswa-msib', $data);
    }

    public function detail(string $encrypted): JsonResponse
    {
        $id = decryptUrl($encrypted);
        
        $user = User::with('proposalMahasiswa')->findOrFail($id);
        
        // Authorization: Mentor can only see their mentees
        abort_unless(
            $user->proposalMahasiswa?->email_mentor === $this->authenticatedUser()->username,
            403
        );
        
        return response()->json([
            'user' => [
                'name' => $user->name,
                'nim' => $user->username,
                'email' => $user->email,
                'email_bsi' => $user->email_bsi,
                'phone' => $user->phone,
                'nama_dosen_pa' => $user->nama_dosen_pa,
                'jenis' => $user->jenis,
                'kd_lokal' => $user->kd_lokal,
            ],
            'proposal' => $user->proposalMahasiswa ? [
                'jns_pkl' => $user->proposalMahasiswa->jns_pkl,
                'judul_pkl' => $user->proposalMahasiswa->judul_pkl,
                'tempat_riset' => $user->proposalMahasiswa->tempat_riset,
                'nama_mentor' => $user->proposalMahasiswa->nama_mentor,
                'hp_mentor' => $user->proposalMahasiswa->hp_mentor,
                'email_mentor' => $user->proposalMahasiswa->email_mentor,
                'email_perusahaan' => $user->proposalMahasiswa->email_perusahaan,
                'files' => [
                    'skm' => $user->proposalMahasiswa->skm,
                    'proposal' => $user->proposalMahasiswa->proposal,
                    'lp' => $user->proposalMahasiswa->lp,
                    'lpp' => $user->proposalMahasiswa->lpp,
                    'skp' => $user->proposalMahasiswa->skp,
                ],
                'nilai' => $user->proposalMahasiswa->nilai,
                'penilai' => $user->proposalMahasiswa->penilai,
            ] : null,
        ]);
    }
}
