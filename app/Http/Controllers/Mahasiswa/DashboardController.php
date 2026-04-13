<?php

namespace App\Http\Controllers\Mahasiswa;

use App\Http\Controllers\Controller;
use App\Models\ProposalMahasiswa;
use App\Services\DashboardService;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __construct(
        protected DashboardService $dashboardService,
    ) {}

    public function index(): View
    {
        $user = $this->authenticatedUser();
        $proposal = ProposalMahasiswa::where('nim', $user->username)->first();
        $deadline = $this->dashboardService->getOpeningHours();

        return view('mahasiswa.dashboard', compact('user', 'proposal', 'deadline'));
    }
}
