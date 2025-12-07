<?php

namespace App\Services;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\MenuItem;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * Order Service
 * 
 * Handle semua business logic terkait order management.
 * Mengikuti Clean Architecture - memisahkan business logic dari controller.
 */
class OrderService
{
    /**
     * Get orders by status dengan pagination
     *
     * @param string|array $status Status order: pending_payment, preparing, ready, completed, cancelled
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function getOrdersByStatus(string|array $status, int $perPage = 10): LengthAwarePaginator
    {
        $query = Order::with(['items.menuItem', 'customer'])
            ->whereDate('created_at', today())
            ->latest();

        if (is_array($status)) {
            $query->whereIn('status', $status);
        } else {
            $query->where('status', $status);
        }

        return $query->paginate($perPage);
    }

    /**
     * Get all pending payment orders
     *
     * @return Collection
     */
    public function getPendingPaymentOrders(): Collection
    {
        return Order::with(['items.menuItem', 'customer'])
            ->where('status', 'pending_payment')
            ->whereDate('created_at', today())
            ->latest()
            ->get();
    }

    /**
     * Get orders being prepared
     *
     * @return Collection
     */
    public function getPreparingOrders(): Collection
    {
        return Order::with(['items.menuItem', 'customer'])
            ->where('status', 'preparing')
            ->whereDate('created_at', today())
            ->latest()
            ->get();
    }

    /**
     * Get ready orders
     *
     * @return Collection
     */
    public function getReadyOrders(): Collection
    {
        return Order::with(['items.menuItem', 'customer'])
            ->where('status', 'ready')
            ->whereDate('created_at', today())
            ->latest()
            ->get();
    }

    /**
     * Get completed orders today dengan pagination
     *
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function getCompletedOrdersToday(int $perPage = 15): LengthAwarePaginator
    {
        return Order::with(['items.menuItem', 'customer'])
            ->where('status', 'completed')
            ->whereDate('created_at', today())
            ->latest()
            ->paginate($perPage);
    }

    /**
     * Get single order by ID
     *
     * @param int $orderId
     * @return Order|null
     */
    public function getOrderById(int $orderId): ?Order
    {
        return Order::with(['items.menuItem', 'customer'])->find($orderId);
    }

    /**
     * Create new order (manual order dari cashier)
     *
     * @param array $data Order data dengan items
     * @return Order
     */
    public function createOrder(array $data): Order
    {
        return DB::transaction(function () use ($data) {
            // Generate order number
            $orderNumber = $this->generateOrderNumber();

            // Calculate total
            $totalAmount = 0;
            $items = $data['items'] ?? [];

            foreach ($items as $item) {
                $menuItem = MenuItem::find($item['menu_item_id']);
                if ($menuItem) {
                    $totalAmount += $menuItem->getCurrentPrice() * $item['quantity'];
                }
            }

            // Apply discount if any
            $discountAmount = $data['discount_amount'] ?? 0;
            $finalAmount = $totalAmount - $discountAmount;

            // Create order
            $order = Order::create([
                'order_number' => $orderNumber,
                'user_id' => $data['user_id'] ?? null,
                'cashier_id' => auth()->id(),
                'table_number' => $data['table_number'] ?? null,
                'order_type' => $data['order_type'] ?? 'dine_in',
                'status' => 'pending_payment',
                'total_amount' => $finalAmount,
                'discount_amount' => $discountAmount,
                'promo_code' => $data['promo_code'] ?? null,
                'customer_mood_summary' => $data['customer_mood_summary'] ?? null,
                'notes' => $data['notes'] ?? null,
            ]);

            // Create order items
            foreach ($items as $item) {
                $menuItem = MenuItem::find($item['menu_item_id']);
                if ($menuItem) {
                    OrderItem::create([
                        'order_id' => $order->id,
                        'menu_item_id' => $menuItem->id,
                        'quantity' => $item['quantity'],
                        'price_at_moment' => $menuItem->getCurrentPrice(),
                        'note' => $item['note'] ?? null,
                    ]);

                    // Decrease stock
                    $menuItem->decrement('stock_quantity', $item['quantity']);
                }
            }

            return $order->fresh(['orderItems.menuItem']);
        });
    }

    /**
     * Process payment
     *
     * @param int $orderId
     * @param string $paymentMethod cash, qris, debit, credit
     * @param float $amountPaid
     * @return Order
     */
    public function processPayment(int $orderId, string $paymentMethod, float $amountPaid = 0): Order
    {
        $order = Order::findOrFail($orderId);

        $changeAmount = $amountPaid > 0 ? $amountPaid - $order->total_amount : 0;

        $order->update([
            'status' => Order::STATUS_PREPARING,
            'payment_method' => $paymentMethod,
            'amount_paid' => $amountPaid,
            'change_amount' => max(0, $changeAmount),
            'paid_at' => now(),
            'cashier_id' => auth()->id(), // Track who processed the payment
        ]);

        return $order->fresh();
    }

    /**
     * Update order status
     *
     * @param int $orderId
     * @param string $status
     * @return Order
     */
    public function updateOrderStatus(int $orderId, string $status): Order
    {
        $order = Order::findOrFail($orderId);

        $updateData = ['status' => $status];

        // Set completed_at if status is completed
        if ($status === 'completed') {
            $updateData['completed_at'] = now();
        }

        $order->update($updateData);

        return $order->fresh();
    }

    /**
     * Cancel order
     *
     * @param int $orderId
     * @param string|null $reason
     * @return Order
     */
    public function cancelOrder(int $orderId, ?string $reason = null): Order
    {
        return DB::transaction(function () use ($orderId, $reason) {
            $order = Order::with('orderItems')->findOrFail($orderId);

            // Restore stock
            foreach ($order->orderItems as $item) {
                if ($item->menuItem) {
                    $item->menuItem->increment('stock_quantity', $item->quantity);
                }
            }

            $order->update([
                'status' => 'cancelled',
                'notes' => $reason ? ($order->notes . "\nCancelled: " . $reason) : $order->notes,
            ]);

            return $order->fresh();
        });
    }

    /**
     * Generate unique order number
     *
     * @return string
     */
    protected function generateOrderNumber(): string
    {
        $prefix = 'MB';
        $date = now()->format('ymd');
        $random = strtoupper(Str::random(4));

        $orderNumber = "{$prefix}{$date}{$random}";

        // Ensure uniqueness
        while (Order::where('order_number', $orderNumber)->exists()) {
            $random = strtoupper(Str::random(4));
            $orderNumber = "{$prefix}{$date}{$random}";
        }

        return $orderNumber;
    }

    /**
     * Get today's statistics for dashboard
     *
     * @return array
     */
    public function getTodayStats(): array
    {
        $today = today();

        return [
            'totalOrders' => Order::whereDate('created_at', $today)->count(),
            'pendingPayment' => Order::where('status', 'pending_payment')
                ->whereDate('created_at', $today)->count(),
            'preparing' => Order::where('status', 'preparing')
                ->whereDate('created_at', $today)->count(),
            'ready' => Order::where('status', 'ready')
                ->whereDate('created_at', $today)->count(),
            'completed' => Order::where('status', 'completed')
                ->whereDate('created_at', $today)->count(),
            'cancelled' => Order::where('status', 'cancelled')
                ->whereDate('created_at', $today)->count(),
            'totalRevenue' => Order::where('status', 'completed')
                ->whereDate('created_at', $today)
                ->sum('total_amount'),
        ];
    }

    /**
     * Get available menu items for ordering
     *
     * @return Collection
     */
    public function getAvailableMenuItems(): Collection
    {
        return MenuItem::where('is_available', true)
            ->where('stock_quantity', '>', 0)
            ->orderBy('category')
            ->orderBy('name')
            ->get();
    }

    /**
     * Search orders
     *
     * @param string $query Order number or table number
     * @return Collection
     */
    public function searchOrders(string $query): Collection
    {
        return Order::with(['orderItems.menuItem', 'user'])
            ->where(function ($q) use ($query) {
                $q->where('order_number', 'like', "%{$query}%")
                    ->orWhere('table_number', 'like', "%{$query}%");
            })
            ->whereDate('created_at', today())
            ->latest()
            ->limit(20)
            ->get();
    }
}
