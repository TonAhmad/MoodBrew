<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Services\OrderService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * OrderController - Handle order management untuk admin
 * Admin bisa melihat semua orders dan statistiknya
 */
class OrderController extends Controller
{
    protected OrderService $orderService;

    public function __construct(OrderService $orderService)
    {
        $this->orderService = $orderService;
    }

    /**
     * Display list of all orders
     */
    public function index(Request $request): View
    {
        $query = Order::with(['customer', 'cashier', 'orderItems.menuItem']);

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by date range
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        // Search by order number or customer
        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('order_number', 'like', '%' . $request->search . '%')
                    ->orWhereHas('customer', function ($cq) use ($request) {
                        $cq->where('name', 'like', '%' . $request->search . '%');
                    });
            });
        }

        $orders = $query->orderBy('created_at', 'desc')->paginate(20);

        // Get order statistics
        $stats = [
            'total' => Order::count(),
            'pending' => Order::where('status', Order::STATUS_PENDING_PAYMENT)->count(),
            'preparing' => Order::where('status', Order::STATUS_PREPARING)->count(),
            'completed' => Order::where('status', Order::STATUS_COMPLETED)->count(),
            'cancelled' => Order::where('status', Order::STATUS_CANCELLED)->count(),
        ];

        return view('admin.orders.index', compact('orders', 'stats'));
    }

    /**
     * Show order detail
     */
    public function show(Order $order): View
    {
        $order->load(['customer', 'cashier', 'orderItems.menuItem']);

        return view('admin.orders.show', compact('order'));
    }

    /**
     * Update order status (admin can change any status)
     */
    public function updateStatus(Request $request, Order $order): RedirectResponse
    {
        $request->validate([
            'status' => 'required|in:' . implode(',', [
                Order::STATUS_PENDING_PAYMENT,
                Order::STATUS_PREPARING,
                Order::STATUS_READY,
                Order::STATUS_COMPLETED,
                Order::STATUS_CANCELLED,
            ]),
        ]);

        $order->update(['status' => $request->status]);

        return redirect()
            ->back()
            ->with('success', 'Status order berhasil diperbarui!');
    }
}
