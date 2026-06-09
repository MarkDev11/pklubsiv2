<?php

namespace App\Http\Controllers\Dosen;

use App\Http\Controllers\Controller;
use App\Http\Requests\SaveNilaiRequest;
use App\Models\OpeningHour;
use App\Models\ProposalMahasiswa;
use App\Services\DashboardService;
use App\Services\NilaiService;
use App\Services\PdfService;
use App\Services\ScopedExportService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\Response;

class NilaiController extends Controller
{
    public function __construct(
        protected NilaiService $nilaiService,
        protected PdfService $pdfService,
        protected DashboardService $dashboardService,
        protected ScopedExportService $scopedExportService,
    ) {}

    public function pklIndex(): View
    {
        $user = $this->authenticatedUser();
        $query = ProposalMahasiswa::magang()
            ->byDosen($user->username)
            ->with('user');

        // Search
        if (request('search')) {
            $search = request('search');
            $query->where(function ($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                    ->orWhere('nim', 'like', "%{$search}%")
                    ->orWhere('tempat_riset', 'like', "%{$search}%")
                    ->orWhere('nama_mentor', 'like', "%{$search}%");
            });
        }

        // Filter by document completeness
        if (request('kelengkapan') === 'lengkap') {
            $query->whereNotNull('lp')
                ->whereNotNull('lpp')
                ->whereNotNull('skp');
        } elseif (request('kelengkapan') === 'tidak_lengkap') {
            $query->where(function ($q) {
                $q->whereNull('lp')
                    ->orWhereNull('lpp')
                    ->orWhereNull('skp');
            });
        }

        // Filter by grading status
        if (request('status_nilai') === 'sudah') {
            $query->where('nilai', '>', 0);
        } elseif (request('status_nilai') === 'belum') {
            $query->where(function ($q) {
                $q->whereNull('nilai')->orWhere('nilai', '<=', 0);
            });
        }

        $proposals = $query->paginate(50)->withQueryString();
        $openingHours = $this->dashboardService->getOpeningHours();
        $exportHistory = $this->scopedExportService->history($user, 'pkl');

        return view('dosen.nilai-pkl', compact('proposals', 'openingHours', 'exportHistory'));
    }

    public function msibIndex(): View
    {
        $user = $this->authenticatedUser();
        $query = ProposalMahasiswa::msib()
            ->byDosen($user->username)
            ->with('user');

        // Search
        if (request('search')) {
            $search = request('search');
            $query->where(function ($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                    ->orWhere('nim', 'like', "%{$search}%")
                    ->orWhere('tempat_riset', 'like', "%{$search}%")
                    ->orWhere('nama_mentor', 'like', "%{$search}%");
            });
        }

        // Filter by document completeness
        if (request('kelengkapan') === 'lengkap') {
            $query->whereNotNull('lp')
                ->whereNotNull('lpp')
                ->whereNotNull('skp');
        } elseif (request('kelengkapan') === 'tidak_lengkap') {
            $query->where(function ($q) {
                $q->whereNull('lp')
                    ->orWhereNull('lpp')
                    ->orWhereNull('skp');
            });
        }

        // Filter by grading status
        if (request('status_nilai') === 'sudah') {
            $query->where('nilai', '>', 0);
        } elseif (request('status_nilai') === 'belum') {
            $query->where(function ($q) {
                $q->whereNull('nilai')->orWhere('nilai', '<=', 0);
            });
        }

        $proposals = $query->paginate(50)->withQueryString();
        $openingHours = $this->dashboardService->getOpeningHours();
        $exportHistory = $this->scopedExportService->history($user, 'msib');

        return view('dosen.nilai-msib', compact('proposals', 'openingHours', 'exportHistory'));
    }

    public function saveNilai(SaveNilaiRequest $request): mixed
    {
        $user = $this->authenticatedUser();

        $openingHours = OpeningHour::first();

        if ($openingHours && ! $openingHours->isNilaiBuka()) {
            if ($request->wantsJson()) {
                return response()->json(['message' => 'Periode input nilai sedang ditutup.'], 403);
            }

            return back()->with('error', 'Periode input nilai sedang ditutup.');
        }

        $this->nilaiService->saveNilai(
            $request->form_id,
            $request->nilai,
            $user,
            fn ($q) => $q->whereHas('user', fn ($u) => $u->where('nama_dosen_pa', $user->username)),
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

    public function exportPkl(Request $request): RedirectResponse
    {
        return $this->createExport($request, 'pkl');
    }

    public function exportMsib(Request $request): RedirectResponse
    {
        return $this->createExport($request, 'msib');
    }

    protected function createExport(Request $request, string $category): RedirectResponse
    {
        $validated = $request->validate([
            'type' => ['required', 'in:pdf,excel'],
        ]);

        $user = $this->authenticatedUser();
        $query = $this->scopedExportService->dosenQuery($user, $category);
        $this->scopedExportService->create($user, $query, $category, $validated['type']);

        return back()->with('success', "Export {$category} berhasil dibuat. File aktif selama 1 jam.");
    }
}
