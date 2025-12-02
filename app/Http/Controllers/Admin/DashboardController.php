<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\AdminDashboardService;
use Illuminate\View\View;

/**
 * DashboardController - Handle admin dashboard
 */
class DashboardController extends Controller
{
    /**
     * @var AdminDashboardService
     */
    protected AdminDashboardService $dashboardService;

    /**
     * Constructor dengan dependency injection
     */
    public function __construct(AdminDashboardService $dashboardService)
    {
        $this->dashboardService = $dashboardService;
    }

    /**
     * Display admin dashboard
     */
    public function index(): View
    {
        return view('admin.dashboard', [
            'stats' => $this->dashboardService->getDashboardStats(),
            'recentOrders' => $this->dashboardService->getRecentOrders(5),
            'topMenuItems' => $this->dashboardService->getTopMenuItems(5),
        ]);
    }
}
