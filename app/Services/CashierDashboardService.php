<?php

namespace App\Services;

use App\Models\FlashSale;
use App\Models\Order;
use Carbon\Carbon;
use Illuminate\Support\Collection;

/**
 * CashierDashboardService - Business logic untuk cashier dashboard
 */
class CashierDashboardService
{
    /**
     * Get pending orders untuk ditampilkan di dashboard kasir
     * 
     * @return Collection
     */
    public function getPendingOrders(): Collection
    {
        return Order::with(['customer', 'orderItems.menuItem'])
            ->where('status', Order::STATUS_PENDING_PAYMENT)
            ->orderBy('created_at', 'asc')
            ->get();
    }

    /**
     * Get orders yang sedang diproses
     * 
     * @return Collection
     */
    public function getInProgressOrders(): Collection
    {
        return Order::with(['customer', 'orderItems.menuItem'])
            ->whereIn('status', [
                Order::STATUS_PAID_PREPARING,
                Order::STATUS_SERVED,
            ])
            ->orderBy('created_at', 'asc')
            ->get();
    }

    /**
     * Get today's statistics untuk kasir
     * 
     * @return array
     */
    public function getTodayStats(): array
    {
        $today = Carbon::today();

        return [
            'totalOrders' => Order::whereDate('created_at', $today)->count(),
            'pendingPayment' => Order::whereDate('created_at', $today)
                ->where('status', Order::STATUS_PENDING_PAYMENT)
                ->count(),
            'completed' => Order::whereDate('created_at', $today)
                ->where('status', Order::STATUS_COMPLETED)
                ->count(),
            'totalRevenue' => Order::whereDate('created_at', $today)
                ->where('status', Order::STATUS_COMPLETED)
                ->sum('total_amount'),
        ];
    }

    /**
     * Get active flash sale
     * 
     * @return FlashSale|null
     */
    public function getActiveFlashSale(): ?FlashSale
    {
        return FlashSale::active()->first();
    }
}
