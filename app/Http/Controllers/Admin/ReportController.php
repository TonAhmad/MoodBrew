<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\SalesReportService;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * ReportController - Handle laporan penjualan
 */
class ReportController extends Controller
{
    protected SalesReportService $reportService;

    public function __construct(SalesReportService $reportService)
    {
        $this->reportService = $reportService;
    }

    /**
     * Display sales reports overview
     */
    public function index(Request $request): View
    {
        $period = $request->get('period', 'daily');
        $date = $request->get('date', now()->format('Y-m-d'));

        $report = match ($period) {
            'daily' => $this->reportService->getDailyReport($date),
            'weekly' => $this->reportService->getWeeklyReport($date),
            'monthly' => $this->reportService->getMonthlyReport($date),
            default => $this->reportService->getDailyReport($date),
        };

        return view('admin.reports.index', compact('report', 'period', 'date'));
    }

    /**
     * Display daily report detail
     */
    public function daily(Request $request): View
    {
        $date = $request->get('date', now()->format('Y-m-d'));
        $report = $this->reportService->getDailyReport($date);

        return view('admin.reports.daily', compact('report', 'date'));
    }

    /**
     * Display monthly report detail
     */
    public function monthly(Request $request): View
    {
        $month = $request->get('month', now()->format('Y-m'));
        $report = $this->reportService->getMonthlyReport($month . '-01');

        return view('admin.reports.monthly', compact('report', 'month'));
    }
}
