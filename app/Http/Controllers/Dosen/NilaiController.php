<?php

namespace App\Http\Controllers\Dosen;

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
        $user = $this->authenticatedUser();
        $proposals = ProposalMahasiswa::magang()
            ->byDosen($user->name)
            ->with('user')
            ->paginate(50);
        $openingHours = $this->dashboardService->getOpeningHours();

        return view('dosen.nilai-pkl', compact('proposals', 'openingHours'));
    }

    public function msibIndex(): View
    {
        $user = $this->authenticatedUser();
        $proposals = ProposalMahasiswa::msib()
            ->byDosen($user->name)
            ->with('user')
            ->paginate(50);
        $openingHours = $this->dashboardService->getOpeningHours();

        return view('dosen.nilai-msib', compact('proposals', 'openingHours'));
    }

    public function saveNilai(SaveNilaiRequest $request): mixed
    {
        $user = $this->authenticatedUser();

        $this->nilaiService->saveNilai(
            $request->form_id,
            $request->nilai,
            $user->name,
            fn ($q) => $q->whereHas('user', fn ($u) => $u->where('nama_dosen_pa', $user->name)),
        );

        if ($request->wantsJson()) {
            return response()->json(['message' => 'Nilai berhasil disimpan otomatis.']);
        }

        return back()->with('success', 'Nilai berhasil disimpan.');
    }

    public function generatePdfPkl(): Response
    {
        $user = $this->authenticatedUser();

        return $this->pdfService->streamPklPdf($user);
    }

    public function generatePdfMsib(): Response
    {
        $user = $this->authenticatedUser();

        return $this->pdfService->streamMsibPdf($user);
    }
}
