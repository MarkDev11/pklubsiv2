<?php

namespace App\Http\Controllers\Mentor;

use App\Http\Controllers\Controller;
use App\Services\DataMahasiswaService;
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
}
