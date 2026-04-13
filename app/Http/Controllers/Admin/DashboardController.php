<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\DashboardService;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __construct(
        protected DashboardService $dashboardService,
    ) {}

    public function index(): View
    {
        $stats = $this->dashboardService->getAdminStats();
        $recentLogs = $this->dashboardService->getRecentLogs();
        $deadline = $this->dashboardService->getOpeningHours();

        return view('admin.dashboard', array_merge($stats, [
            'recentLogs' => $recentLogs,
            'deadline' => $deadline,
        ]));
    }
}
