<?php

namespace App\Http\Controllers\Mentor;

use App\Http\Controllers\Controller;
use App\Http\Requests\SaveNilaiRequest;
use App\Models\ActivityLog;
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
            ->where('email_mentor', $user->username)
            ->with('user')->paginate(50);
        $openingHours = $this->dashboardService->getOpeningHours();

        return view('mentor.nilai-pkl', compact('proposals', 'openingHours'));
    }

    public function msibIndex(): View
    {
        $user = $this->authenticatedUser();
        $proposals = ProposalMahasiswa::msib()
            ->where('email_mentor', $user->username)
            ->with('user')->paginate(50);
        $openingHours = $this->dashboardService->getOpeningHours();

        return view('mentor.nilai-msib', compact('proposals', 'openingHours'));
    }

    public function saveNilai(SaveNilaiRequest $request): mixed
    {
        $user = $this->authenticatedUser();

        $this->nilaiService->saveNilai(
            $request->form_id,
            $request->nilai,
            $user->name,
            fn ($q) => $q->where('email_mentor', $user->username),
        );

        ActivityLog::log($user->id, 'Input nilai mentor');

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
