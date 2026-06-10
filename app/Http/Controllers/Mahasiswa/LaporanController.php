<?php

namespace App\Http\Controllers\Mahasiswa;

use App\Http\Controllers\Controller;
use App\Http\Requests\Mahasiswa\UploadLaporanRequest;
use App\Models\ActivityLog;
use App\Models\ProposalMahasiswa;
use App\Services\DashboardService;
use App\Services\ProposalService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class LaporanController extends Controller
{
    public function __construct(
        protected ProposalService $proposalService,
        protected DashboardService $dashboardService,
    ) {}

    public function index(): View
    {
        $user = $this->authenticatedUser();
        $proposal = ProposalMahasiswa::where('nim', $user->username)->first();
        $openingHours = $this->dashboardService->getOpeningHours();

        return view('mahasiswa.laporan', compact('user', 'proposal', 'openingHours'));
    }

    public function upload(UploadLaporanRequest $request): RedirectResponse
    {
        $user = $this->authenticatedUser();
        $proposal = ProposalMahasiswa::where('user_id', $user->id)->firstOrFail();

        $this->authorize('uploadLaporan', $proposal);

        $openingHours = $this->dashboardService->getOpeningHours();

        if ($openingHours && ! $openingHours->isLaporanBuka()) {
            return back()->with('error', 'Upload laporan sedang ditutup.');
        }

        $data = [];
        $prefixes = ['lp' => 'laporan', 'lpp' => 'penilaian', 'skp' => 'suratketerangan'];
        
        // Use jns_pkl from proposal for file naming (simplified)
        $jenisFile = match(true) {
            str_contains($proposal->jns_pkl ?? '', 'Program Magang khusus') => 'PMK',
            ($proposal->jns_pkl ?? '') === 'Magang' => 'Magang',
            default => 'PKL'
        };

        foreach (['lp', 'lpp', 'skp'] as $field) {
            if ($request->hasFile($field)) {
                $this->proposalService->deleteOldFile($proposal->{$field});
                $data[$field] = $this->proposalService->uploadFile(
                    $request->file($field),
                    $prefixes[$field].'_'.$jenisFile,
                    $user->username
                );
            }
        }

        if (! empty($data)) {
            $proposal->update($data);
            ActivityLog::log($user->id, 'Upload laporan');
        }

        return redirect()->route('mahasiswa.dashboard')
            ->with('success', 'Laporan berhasil diupload.');
    }

    public function reset(): RedirectResponse
    {
        $user = $this->authenticatedUser();
        $proposal = ProposalMahasiswa::where('nim', $user->username)->first();

        if ($proposal) {
            $this->authorize('resetLaporan', $proposal);

            foreach (['lp', 'lpp', 'skp'] as $field) {
                $this->proposalService->deleteOldFile($proposal->{$field});
            }

            $proposal->update(['lp' => null, 'lpp' => null, 'skp' => null]);
            ActivityLog::log($user->id, 'Reset laporan');
        }

        return redirect()->route('mahasiswa.laporan.index')
            ->with('success', 'Laporan berhasil direset.');
    }
}
