<?php

namespace App\Http\Controllers\Dosen;

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
        $data = $this->dataService->getGroupedForDosen($this->authenticatedUser(), 'Magang');

        return view('dosen.data-mahasiswa-pkl', $data);
    }

    public function msibIndex(): View
    {
        $data = $this->dataService->getGroupedForDosen($this->authenticatedUser(), 'MSIB');

        return view('dosen.data-mahasiswa-msib', $data);
    }
}
