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
        $query = ProposalMahasiswa::magang()->with('user');

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

        return view('admin.nilai.pkl', compact('proposals', 'openingHours'));
    }

    public function msibIndex(): View
    {
        $query = ProposalMahasiswa::msib()->with('user');

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

        return view('admin.nilai.msib', compact('proposals', 'openingHours'));
    }

    public function saveNilai(SaveNilaiRequest $request): mixed
    {
        $user = $this->authenticatedUser();

        $this->nilaiService->saveNilai(
            $request->form_id,
            $request->nilai,
            $user,
            fn ($q) => $q,
        );

        if ($request->wantsJson()) {
            return response()->json(['message' => 'Nilai berhasil disimpan otomatis.']);
        }

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
