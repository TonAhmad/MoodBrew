<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Services\CartService;
use App\Services\CustomerOrderService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * OrderController - Handle checkout dan order tracking untuk customer
 */
class OrderController extends Controller
{
    public function __construct(
        protected CartService $cartService,
        protected CustomerOrderService $orderService
    ) {}

    /**
     * Display checkout page
     */
    public function checkout(): View
    {
        $cartItems = $this->cartService->getCartItems();
        $cartTotal = $this->cartService->getCartTotal();

        if ($cartItems->isEmpty()) {
            return redirect()->route('customer.cart.index')
                ->with('error', 'Keranjang kosong');
        }

        return view('customer.order.checkout', compact(
            'cartItems',
            'cartTotal'
        ));
    }

    /**
     * Process checkout and create order
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'customer_name' => 'required|string|max:100',
            'table_number' => 'required|string|max:10',
            'notes' => 'nullable|string|max:500',
        ]);

        // Update session
        session([
            'customer_name' => $request->customer_name,
            'table_number' => $request->table_number,
        ]);

        $result = $this->cartService->createOrder([
            'customer_name' => $request->customer_name,
            'table_number' => $request->table_number,
            'notes' => $request->notes,
            'mood_summary' => session('selected_mood'),
        ]);

        if (!$result['success']) {
            return back()->with('error', $result['message']);
        }

        return redirect()->route('customer.orders.show', $result['order']->order_number)
            ->with('success', 'Pesanan berhasil dibuat! 🎉');
    }

    /**
     * Display order tracking page
     */
    public function index(): View
    {
        $orders = $this->orderService->getMyOrders(
            auth()->id(),
            session()->getId()
        );

        $activeOrders = $this->orderService->getActiveOrders(auth()->id());

        return view('customer.order.index', compact('orders', 'activeOrders'));
    }

    /**
     * Show single order details
     */
    public function show(string $orderNumber): View
    {
        $order = $this->orderService->getOrderByNumber($orderNumber);

        if (!$order) {
            abort(404, 'Pesanan tidak ditemukan');
        }

        $statusInfo = $this->orderService->getStatusLabel($order->status);

        return view('customer.order.show', compact('order', 'statusInfo'));
    }
}
