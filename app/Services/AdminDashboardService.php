<?php

namespace App\Services;

use App\Models\FlashSale;
use App\Models\MenuItem;
use App\Models\Order;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Collection;

/**
 * AdminDashboardService - Business logic untuk admin dashboard
 */
class AdminDashboardService
{
    /**
     * Get dashboard statistics
     * 
     * @return array
     */
    public function getDashboardStats(): array
    {
        $today = Carbon::today();
        $thisMonth = Carbon::now()->startOfMonth();

        return [
            'totalOrders' => Order::count(),
            'todayOrders' => Order::whereDate('created_at', $today)->count(),
            'monthlyRevenue' => Order::where('status', Order::STATUS_COMPLETED)
                ->where('created_at', '>=', $thisMonth)
                ->sum('total_amount'),
            'todayRevenue' => Order::where('status', Order::STATUS_COMPLETED)
                ->whereDate('created_at', $today)
                ->sum('total_amount'),
            'totalMenuItems' => MenuItem::count(),
            'availableMenuItems' => MenuItem::available()->count(),
            'totalCustomers' => User::where('role', User::ROLE_CUSTOMER)->count(),
            'pendingOrders' => Order::where('status', Order::STATUS_PENDING_PAYMENT)->count(),
        ];
    }

    /**
     * Get recent orders
     * 
     * @param int $limit
     * @return Collection
     */
    public function getRecentOrders(int $limit = 10): Collection
    {
        return Order::with(['customer', 'orderItems.menuItem'])
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Get top selling menu items
     * 
     * @param int $limit
     * @return Collection
     */
    public function getTopMenuItems(int $limit = 5): Collection
    {
        return MenuItem::withCount('orderItems')
            ->orderBy('order_items_count', 'desc')
            ->limit($limit)
            ->get();
    }
}
