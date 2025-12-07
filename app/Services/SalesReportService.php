<?php

namespace App\Services;

use App\Models\MenuItem;
use App\Models\Order;
use App\Models\OrderItem;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

/**
 * SalesReportService - Business logic untuk laporan penjualan
 */
class SalesReportService
{
    /**
     * Get daily sales report
     */
    public function getDailyReport(string $date): array
    {
        $targetDate = Carbon::parse($date);

        $orders = Order::whereDate('created_at', $targetDate)
            ->where('status', Order::STATUS_COMPLETED)
            ->with(['orderItems.menuItem', 'customer', 'cashier'])
            ->get();

        $totalRevenue = $orders->sum('total_amount');
        $totalOrders = $orders->count();
        $avgOrderValue = $totalOrders > 0 ? $totalRevenue / $totalOrders : 0;

        // Best selling items - use startOfDay/endOfDay for whereBetween to work
        $bestSellers = $this->getBestSellingItems($targetDate->copy()->startOfDay(), $targetDate->copy()->endOfDay());

        // Hourly breakdown
        $hourlyBreakdown = $this->getHourlyBreakdown($targetDate);

        // Revenue by category - use startOfDay/endOfDay for whereBetween to work
        $revenueByCategory = $this->getRevenueByCategory($targetDate->copy()->startOfDay(), $targetDate->copy()->endOfDay());

        // Payment method breakdown
        $paymentMethods = Order::whereDate('created_at', $targetDate)
            ->where('status', Order::STATUS_COMPLETED)
            ->selectRaw('payment_method, COUNT(*) as count, SUM(total_amount) as total')
            ->groupBy('payment_method')
            ->pluck('total', 'payment_method')
            ->toArray();

        return [
            'date' => $targetDate->format('Y-m-d'),
            'formatted_date' => $targetDate->translatedFormat('l, d F Y'),
            'total_revenue' => $totalRevenue,
            'total_orders' => $totalOrders,
            'avg_order_value' => round($avgOrderValue, 0),
            'best_sellers' => $bestSellers,
            'hourly_breakdown' => $hourlyBreakdown,
            'revenue_by_category' => $revenueByCategory,
            'payment_methods' => $paymentMethods,
            'orders' => $orders,
        ];
    }

    /**
     * Get weekly sales report
     */
    public function getWeeklyReport(string $date): array
    {
        $targetDate = Carbon::parse($date);
        $startOfWeek = $targetDate->copy()->startOfWeek();
        $endOfWeek = $targetDate->copy()->endOfWeek();

        $dailyTotals = Order::whereBetween('created_at', [$startOfWeek, $endOfWeek])
            ->where('status', Order::STATUS_COMPLETED)
            ->selectRaw('DATE(created_at) as date, COUNT(*) as orders, SUM(total_amount) as revenue')
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->keyBy('date');

        // Fill in missing days
        $weekData = [];
        for ($i = 0; $i < 7; $i++) {
            $day = $startOfWeek->copy()->addDays($i);
            $dayStr = $day->format('Y-m-d');
            $weekData[] = [
                'date' => $dayStr,
                'day_name' => $day->translatedFormat('l'),
                'orders' => $dailyTotals->get($dayStr)?->orders ?? 0,
                'revenue' => $dailyTotals->get($dayStr)?->revenue ?? 0,
            ];
        }

        $totalRevenue = collect($weekData)->sum('revenue');
        $totalOrders = collect($weekData)->sum('orders');

        return [
            'start_date' => $startOfWeek->format('Y-m-d'),
            'end_date' => $endOfWeek->format('Y-m-d'),
            'formatted_period' => $startOfWeek->translatedFormat('d M') . ' - ' . $endOfWeek->translatedFormat('d M Y'),
            'total_revenue' => $totalRevenue,
            'total_orders' => $totalOrders,
            'avg_order_value' => $totalOrders > 0 ? round($totalRevenue / $totalOrders, 0) : 0,
            'daily_data' => $weekData,
            'best_sellers' => $this->getBestSellingItems($startOfWeek, $endOfWeek),
            'revenue_by_category' => $this->getRevenueByCategory($startOfWeek, $endOfWeek),
        ];
    }

    /**
     * Get monthly sales report
     */
    public function getMonthlyReport(string $date): array
    {
        $targetDate = Carbon::parse($date);
        $startOfMonth = $targetDate->copy()->startOfMonth();
        $endOfMonth = $targetDate->copy()->endOfMonth();

        $dailyTotals = Order::whereBetween('created_at', [$startOfMonth, $endOfMonth])
            ->where('status', Order::STATUS_COMPLETED)
            ->selectRaw('DATE(created_at) as date, COUNT(*) as orders, SUM(total_amount) as revenue')
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->keyBy('date');

        // Fill in missing days
        $monthData = [];
        $daysInMonth = $targetDate->daysInMonth;
        for ($i = 1; $i <= $daysInMonth; $i++) {
            $day = $startOfMonth->copy()->addDays($i - 1);
            $dayStr = $day->format('Y-m-d');
            $monthData[] = [
                'date' => $dayStr,
                'day' => $i,
                'orders' => $dailyTotals->get($dayStr)?->orders ?? 0,
                'revenue' => $dailyTotals->get($dayStr)?->revenue ?? 0,
            ];
        }

        $totalRevenue = collect($monthData)->sum('revenue');
        $totalOrders = collect($monthData)->sum('orders');

        // Weekly comparison
        $weeklyComparison = $this->getWeeklyComparison($startOfMonth, $endOfMonth);

        return [
            'month' => $targetDate->format('Y-m'),
            'formatted_period' => $targetDate->translatedFormat('F Y'),
            'total_revenue' => $totalRevenue,
            'total_orders' => $totalOrders,
            'avg_order_value' => $totalOrders > 0 ? round($totalRevenue / $totalOrders, 0) : 0,
            'avg_daily_revenue' => round($totalRevenue / $daysInMonth, 0),
            'daily_data' => $monthData,
            'weekly_comparison' => $weeklyComparison,
            'best_sellers' => $this->getBestSellingItems($startOfMonth, $endOfMonth, 10),
            'revenue_by_category' => $this->getRevenueByCategory($startOfMonth, $endOfMonth),
        ];
    }

    /**
     * Get best selling items in a period
     */
    private function getBestSellingItems(Carbon $start, Carbon $end, int $limit = 5): Collection
    {
        return OrderItem::join('orders', 'order_items.order_id', '=', 'orders.id')
            ->join('menu_items', 'order_items.menu_item_id', '=', 'menu_items.id')
            ->whereBetween('orders.created_at', [$start, $end])
            ->where('orders.status', Order::STATUS_COMPLETED)
            ->selectRaw('menu_items.id, menu_items.name, menu_items.category, SUM(order_items.quantity) as total_qty, SUM(order_items.price_at_moment * order_items.quantity) as total_revenue')
            ->groupBy('menu_items.id', 'menu_items.name', 'menu_items.category')
            ->orderByDesc('total_qty')
            ->limit($limit)
            ->get();
    }

    /**
     * Get hourly breakdown for a specific day
     */
    private function getHourlyBreakdown(Carbon $date): array
    {
        $hourlyData = Order::whereDate('created_at', $date)
            ->where('status', Order::STATUS_COMPLETED)
            ->selectRaw('HOUR(created_at) as hour, COUNT(*) as orders, SUM(total_amount) as revenue')
            ->groupBy('hour')
            ->pluck('revenue', 'hour')
            ->toArray();

        $breakdown = [];
        for ($h = 8; $h <= 22; $h++) { // Assuming cafe hours 8AM - 10PM
            $breakdown[] = [
                'hour' => sprintf('%02d:00', $h),
                'revenue' => $hourlyData[$h] ?? 0,
            ];
        }

        return $breakdown;
    }

    /**
     * Get revenue breakdown by category
     */
    private function getRevenueByCategory(Carbon $start, Carbon $end): array
    {
        return OrderItem::join('orders', 'order_items.order_id', '=', 'orders.id')
            ->join('menu_items', 'order_items.menu_item_id', '=', 'menu_items.id')
            ->whereBetween('orders.created_at', [$start, $end])
            ->where('orders.status', Order::STATUS_COMPLETED)
            ->selectRaw('menu_items.category, SUM(order_items.price_at_moment * order_items.quantity) as total')
            ->groupBy('menu_items.category')
            ->pluck('total', 'category')
            ->toArray();
    }

    /**
     * Get weekly comparison within a month
     */
    private function getWeeklyComparison(Carbon $start, Carbon $end): array
    {
        return Order::whereBetween('created_at', [$start, $end])
            ->where('status', Order::STATUS_COMPLETED)
            ->selectRaw('WEEK(created_at) as week, COUNT(*) as orders, SUM(total_amount) as revenue')
            ->groupBy('week')
            ->orderBy('week')
            ->get()
            ->map(function ($item, $index) {
                return [
                    'week' => 'Minggu ' . ($index + 1),
                    'orders' => $item->orders,
                    'revenue' => $item->revenue,
                ];
            })
            ->toArray();
    }
}
