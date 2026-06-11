<?php

namespace App\Http\Controllers\Mentor;

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

    public function index(): View
    {
        $data = $this->dataService->getAllGroupedForMentor($this->authenticatedUser());

        return view('mentor.data-mahasiswa-pkl', $data);
    }

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

        $proposal = ProposalMahasiswa::with(['user.dosenPa'])
            ->where('email_mentor', $this->authenticatedUser()->username)
            ->findOrFail($id);

        $user = $proposal->user ?? User::where('username', $proposal->nim)->firstOrFail();

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
            'proposal' => [
                'jns_pkl' => $proposal->jns_pkl,
                'judul_pkl' => $proposal->judul_pkl,
                'tempat_riset' => $proposal->tempat_riset,
                'nama_mentor' => $proposal->nama_mentor,
                'hp_mentor' => $proposal->hp_mentor,
                'email_mentor' => $proposal->email_mentor,
                'email_perusahaan' => $proposal->email_perusahaan,
                'files' => [
                    'skm' => $proposal->skm,
                    'proposal' => $proposal->proposal,
                    'lp' => $proposal->lp,
                    'lpp' => $proposal->lpp,
                    'skp' => $proposal->skp,
                ],
                'nilai' => $proposal->nilai,
                'penilai' => $proposal->penilai,
            ],
        ]);
    }
}
