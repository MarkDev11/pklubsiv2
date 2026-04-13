<?php

namespace App\Http\Controllers\Mentor;

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
        $stats = $this->dashboardService->getMentorStats($user->username);
        $deadline = $this->dashboardService->getOpeningHours();

        return view('mentor.dashboard', array_merge(
            ['user' => $user, 'deadline' => $deadline],
            $stats
        ));
    }
}
