<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\SaveNilaiRequest;
use App\Models\ProposalMahasiswa;
use App\Services\DashboardService;
use App\Services\NilaiService;
use App\Services\PdfService;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\Response;

class NilaiController extends Controller
{
    public function __construct(
        protected NilaiService $nilaiService,
        protected PdfService $pdfService,
        protected DashboardService $dashboardService,
    ) {}

    public function pklIndex(): View
    {
        $proposals = ProposalMahasiswa::magang()->with('user')->paginate(50);
        $openingHours = $this->dashboardService->getOpeningHours();

        return view('admin.nilai.pkl', compact('proposals', 'openingHours'));
    }

    public function msibIndex(): View
    {
        $proposals = ProposalMahasiswa::msib()->with('user')->paginate(50);
        $openingHours = $this->dashboardService->getOpeningHours();

        return view('admin.nilai.msib', compact('proposals', 'openingHours'));
    }

    public function saveNilai(SaveNilaiRequest $request): mixed
    {
        $user = $this->authenticatedUser();

        $this->nilaiService->saveNilai(
            $request->form_id,
            $request->nilai,
            $user->name,
            fn ($q) => $q,
        );

        return back()->with('success', 'Nilai berhasil disimpan.');
    }

    public function pklPdf(): Response
    {
        return $this->pdfService->streamPklPdf($this->authenticatedUser());
    }

    public function msibPdf(): Response
    {
        return $this->pdfService->streamMsibPdf($this->authenticatedUser());
    }
}
