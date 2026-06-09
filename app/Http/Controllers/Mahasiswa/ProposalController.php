<?php

namespace App\Http\Controllers\Mahasiswa;

use App\Http\Controllers\Controller;
use App\Http\Requests\Mahasiswa\StoreProposalRequest;
use App\Http\Requests\Mahasiswa\UpdateProposalRequest;
use App\Models\OpeningHour;
use App\Models\ProposalMahasiswa;
use App\Services\ProposalService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ProposalController extends Controller
{
    public function __construct(
        protected ProposalService $proposalService
    ) {}

    public function index(): View
    {
        $user = $this->authenticatedUser()->load('dosenPa');
        $proposal = ProposalMahasiswa::where('nim', $user->username)->first();
        $openingHours = OpeningHour::first();

        return view('mahasiswa.proposal', compact('user', 'proposal', 'openingHours'));
    }

    public function store(StoreProposalRequest $request): RedirectResponse
    {
        $user = $this->authenticatedUser();

        $this->authorize('create', ProposalMahasiswa::class);

        // Check for existing proposal by nim (natural key)
        $existingProposal = ProposalMahasiswa::where('nim', $user->username)->first();
        if ($existingProposal) {
            return redirect()->route('mahasiswa.proposal.index')
                ->with('error', 'Anda sudah memiliki proposal PKL. Silakan perbarui data yang ada.');
        }

        $openingHours = OpeningHour::first();

        if ($openingHours && ! $openingHours->isPendaftaranBuka()) {
            return back()->with('error', 'Pendaftaran PKL sedang ditutup.');
        }

        $this->proposalService->store(
            $user,
            $request->validated(),
            $request->file('skm')
        );

        return redirect()->route('mahasiswa.proposal.index')
            ->with('success', 'Proposal PKL berhasil disimpan.');
    }

    public function update(UpdateProposalRequest $request): RedirectResponse
    {
        $user = $this->authenticatedUser();
        $proposal = ProposalMahasiswa::where('nim', $user->username)->firstOrFail();

        $this->authorize('update', $proposal);

        $openingHours = OpeningHour::first();

        if ($openingHours && ! $openingHours->isPendaftaranBuka()) {
            return back()->with('error', 'Periode input data PKL sedang ditutup.');
        }

        $this->proposalService->update(
            $proposal,
            $user,
            $request->validated(),
            $request->file('skm')
        );

        return redirect()->route('mahasiswa.proposal.index')
            ->with('success', 'Proposal PKL berhasil diperbarui.');
    }
}
