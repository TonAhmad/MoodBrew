<?php

namespace App\Services;

use App\Models\Order;
use Illuminate\Support\Collection;

/**
 * CustomerOrderService - Business logic untuk order tracking customer
 */
class CustomerOrderService
{
    /**
     * Get customer orders by session or user
     */
    public function getMyOrders(?int $userId = null, ?string $sessionId = null): Collection
    {
        $query = Order::with(['orderItems.menuItem']);

        if ($userId) {
            $query->where('user_id', $userId);
        } elseif ($sessionId) {
            // For guest, get orders by customer name + table in session
            $customerName = session('customer_name');
            $tableNumber = session('table_number');
            
            if ($customerName && $tableNumber) {
                $query->where('customer_name', $customerName)
                      ->where('table_number', $tableNumber)
                      ->whereDate('created_at', today());
            } else {
                return collect([]);
            }
        } else {
            return collect([]);
        }

        return $query->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();
    }

    /**
     * Get single order by order number
     */
    public function getOrderByNumber(string $orderNumber): ?Order
    {
        return Order::with(['orderItems.menuItem'])
            ->where('order_number', $orderNumber)
            ->first();
    }

    /**
     * Get active orders (pending or preparing)
     */
    public function getActiveOrders(?int $userId = null): Collection
    {
        $query = Order::with(['orderItems.menuItem'])
            ->whereIn('status', [Order::STATUS_PENDING_PAYMENT, Order::STATUS_PREPARING]);

        if ($userId) {
            $query->where('user_id', $userId);
        } else {
            $customerName = session('customer_name');
            $tableNumber = session('table_number');
            
            if ($customerName && $tableNumber) {
                $query->where('customer_name', $customerName)
                      ->where('table_number', $tableNumber);
            } else {
                return collect([]);
            }
        }

        return $query->orderBy('created_at', 'desc')->get();
    }

    /**
     * Get order status label
     */
    public function getStatusLabel(string $status): array
    {
        return match ($status) {
            Order::STATUS_PENDING_PAYMENT => [
                'label' => 'Menunggu Pembayaran',
                'color' => 'yellow',
                'icon' => '⏳',
                'description' => 'Pesanan menunggu pembayaran di kasir',
            ],
            Order::STATUS_PREPARING => [
                'label' => 'Diproses',
                'color' => 'blue',
                'icon' => '👨‍🍳',
                'description' => 'Pesanan sedang disiapkan',
            ],
            Order::STATUS_READY => [
                'label' => 'Siap',
                'color' => 'green',
                'icon' => '✅',
                'description' => 'Pesanan siap diambil!',
            ],
            Order::STATUS_COMPLETED => [
                'label' => 'Selesai',
                'color' => 'gray',
                'icon' => '🎉',
                'description' => 'Pesanan telah selesai',
            ],
            Order::STATUS_CANCELLED => [
                'label' => 'Dibatalkan',
                'color' => 'red',
                'icon' => '❌',
                'description' => 'Pesanan dibatalkan',
            ],
            default => [
                'label' => ucfirst($status),
                'color' => 'gray',
                'icon' => '📋',
                'description' => '',
            ],
        };
    }
}
