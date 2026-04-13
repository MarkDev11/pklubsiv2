<?php

namespace App\Http\Controllers\Dosen;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\DashboardService;
use Illuminate\Support\Facades\Cache;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __construct(
        protected DashboardService $dashboardService
    ) {}

    public function index(): View
    {
        $user = $this->authenticatedUser();
        $stats = $this->dashboardService->getDosenStats($user->name);
        $deadline = $this->dashboardService->getOpeningHours();

        $listMahasiswa = $this->dashboardService->getListMahasiswa($user);

        return view('dosen.dashboard', array_merge(
            compact('user', 'deadline', 'listMahasiswa'),
            $stats
        ));
    }
}
