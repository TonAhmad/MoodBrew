<?php

namespace App\Http\Controllers\Cashier;

use App\Http\Controllers\Controller;
use App\Services\CashierDashboardService;
use Illuminate\View\View;

/**
 * DashboardController - Handle cashier dashboard
 */
class DashboardController extends Controller
{
    /**
     * @var CashierDashboardService
     */
    protected CashierDashboardService $dashboardService;

    /**
     * Constructor dengan dependency injection
     */
    public function __construct(CashierDashboardService $dashboardService)
    {
        $this->dashboardService = $dashboardService;
    }

    /**
     * Display cashier dashboard dengan pending orders
     */
    public function index(): View
    {
        return view('cashier.dashboard', [
            'pendingOrders' => $this->dashboardService->getPendingOrders(),
            'todayStats' => $this->dashboardService->getTodayStats(),
            'activeFlashSale' => $this->dashboardService->getActiveFlashSale(),
        ]);
    }
}
