<?php

namespace App\Http\Controllers\Dosen;

use App\Http\Controllers\Controller;
use App\Services\DashboardService;
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

        return view('dosen.dashboard', array_merge(
            compact('user', 'deadline'),
            $stats
        ));
    }
}
