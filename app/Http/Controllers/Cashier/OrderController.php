<?php

namespace App\Http\Controllers\Cashier;

use App\Http\Controllers\Controller;
use App\Services\OrderService;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

/**
 * Order Controller for Cashier
 * 
 * Handle semua operasi pesanan dari sisi kasir.
 */
class OrderController extends Controller
{
    protected OrderService $orderService;

    public function __construct(OrderService $orderService)
    {
        $this->orderService = $orderService;
    }

    /**
     * Display pending payment orders
     *
     * @return View
     */
    public function pending(): View
    {
        $orders = $this->orderService->getPendingPaymentOrders();
        $stats = $this->orderService->getTodayStats();

        return view('cashier.orders.pending', [
            'orders' => $orders,
            'stats' => $stats,
        ]);
    }

    /**
     * Display orders being prepared
     *
     * @return View
     */
    public function preparing(): View
    {
        $orders = $this->orderService->getPreparingOrders();
        $stats = $this->orderService->getTodayStats();

        return view('cashier.orders.preparing', [
            'orders' => $orders,
            'stats' => $stats,
        ]);
    }

    /**
     * Display completed/ready orders
     *
     * @return View
     */
    public function completed(): View
    {
        $orders = $this->orderService->getOrdersByStatus(['ready', 'completed'], 20);
        $stats = $this->orderService->getTodayStats();

        return view('cashier.orders.completed', [
            'orders' => $orders,
            'stats' => $stats,
        ]);
    }

    /**
     * Show new order form
     *
     * @return View
     */
    public function create(): View
    {
        $menuItems = $this->orderService->getAvailableMenuItems();

        return view('cashier.orders.create', [
            'menuItems' => $menuItems,
        ]);
    }

    /**
     * Store new order
     *
     * @param Request $request
     * @return RedirectResponse
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'customer_name' => 'nullable|string|max:100',
            'items' => 'required|array|min:1',
            'items.*.menu_item_id' => 'required|exists:menu_items,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.notes' => 'nullable|string|max:255',
            'notes' => 'nullable|string|max:500',
        ]);

        try {
            $order = $this->orderService->createOrder($validated);

            return redirect()
                ->route('cashier.orders.pending')
                ->with('success', "Pesanan #{$order->order_number} berhasil dibuat!");
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Gagal membuat pesanan: ' . $e->getMessage());
        }
    }

    /**
     * Show order detail
     *
     * @param int $id
     * @return View
     */
    public function show(int $id): View
    {
        $order = $this->orderService->getOrderById($id);

        return view('cashier.orders.show', [
            'order' => $order,
        ]);
    }

    /**
     * Process payment for order
     *
     * @param Request $request
     * @param int $id
     * @return RedirectResponse
     */
    public function processPayment(Request $request, int $id): RedirectResponse
    {
        $validated = $request->validate([
            'payment_method' => 'required|in:cash,qris,debit,credit',
            'amount_paid' => 'required|numeric|min:0',
        ]);

        try {
            $order = $this->orderService->processPayment($id, $validated);

            return redirect()
                ->route('cashier.orders.preparing')
                ->with('success', "Pembayaran pesanan #{$order->order_number} berhasil diproses!");
        } catch (\Exception $e) {
            return back()
                ->with('error', 'Gagal memproses pembayaran: ' . $e->getMessage());
        }
    }

    /**
     * Update order status
     *
     * @param Request $request
     * @param int $id
     * @return RedirectResponse
     */
    public function updateStatus(Request $request, int $id): RedirectResponse
    {
        $validated = $request->validate([
            'status' => 'required|in:pending,preparing,ready,completed,cancelled',
        ]);

        try {
            $order = $this->orderService->updateOrderStatus($id, $validated['status']);

            $statusLabels = [
                'pending' => 'Menunggu Pembayaran',
                'preparing' => 'Sedang Disiapkan',
                'ready' => 'Siap Diambil',
                'completed' => 'Selesai',
                'cancelled' => 'Dibatalkan',
            ];

            return back()
                ->with('success', "Status pesanan #{$order->order_number} diubah ke {$statusLabels[$validated['status']]}");
        } catch (\Exception $e) {
            return back()
                ->with('error', 'Gagal mengubah status: ' . $e->getMessage());
        }
    }

    /**
     * Cancel order
     *
     * @param int $id
     * @return RedirectResponse
     */
    public function cancel(int $id): RedirectResponse
    {
        try {
            $order = $this->orderService->cancelOrder($id);

            return redirect()
                ->route('cashier.orders.pending')
                ->with('success', "Pesanan #{$order->order_number} berhasil dibatalkan.");
        } catch (\Exception $e) {
            return back()
                ->with('error', 'Gagal membatalkan pesanan: ' . $e->getMessage());
        }
    }
}
