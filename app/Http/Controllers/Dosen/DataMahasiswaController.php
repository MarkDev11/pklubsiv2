<?php

namespace App\Http\Controllers\Dosen;

use App\Http\Controllers\Controller;
use App\Models\ProposalMahasiswa;
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
        $data = $this->dataService->getGroupedForDosen($this->authenticatedUser(), 'Magang');

        return view('dosen.data-mahasiswa-pkl', $data);
    }

    public function msibIndex(): View
    {
        $data = $this->dataService->getGroupedForDosen($this->authenticatedUser(), 'MSIB');

        return view('dosen.data-mahasiswa-msib', $data);
    }

    public function detail(string $encrypted): JsonResponse
    {
        $id = decryptUrl($encrypted);

        $user = User::with('dosenPa')->findOrFail($id);
        $proposal = ProposalMahasiswa::where('nim', $user->username)->first();

        // Authorization: Dosen can only see their PA students
        abort_unless($user->nama_dosen_pa === $this->authenticatedUser()->username, 403);

        return response()->json([
            'user' => [
                'name' => $user->name,
                'nim' => $user->username,
                'email' => $user->email,
                'email_bsi' => $user->email_bsi,
                'phone' => $user->phone,
                'nama_dosen_pa' => $user->dosenPaLabel(),
                'kd_lokal' => $user->kd_lokal,
            ],
            'proposal' => $proposal ? [
                'jns_pkl' => $proposal->jns_pkl,
                'judul_pkl' => $proposal->judul_pkl,
                'tempat_riset' => $proposal->tempat_riset,
                'nama_mentor' => $proposal->nama_mentor,
                'hp_mentor' => $proposal->hp_mentor,
                'email_mentor' => $proposal->email_mentor,
                'email_perusahaan' => $proposal->email_perusahaan,
                'files' => [
                    'skm' => $proposal->skm,
                    'lp' => $proposal->lp,
                    'lpp' => $proposal->lpp,
                    'skp' => $proposal->skp,
                ],
                'nilai' => $proposal->nilai,
                'penilai' => $proposal->penilai,
            ] : null,
        ]);
    }
}
