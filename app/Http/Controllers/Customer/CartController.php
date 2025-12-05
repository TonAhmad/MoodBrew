<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Services\CartService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * CartController - Handle shopping cart untuk customer
 */
class CartController extends Controller
{
    public function __construct(
        protected CartService $cartService
    ) {}

    /**
     * Display cart page
     */
    public function index(): View
    {
        $cartItems = $this->cartService->getCartItems();
        $cartTotal = $this->cartService->getCartTotal();
        $cartCount = $this->cartService->getCartCount();

        return view('customer.cart.index', compact(
            'cartItems',
            'cartTotal',
            'cartCount'
        ));
    }

    /**
     * Add item to cart (AJAX)
     */
    public function add(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'menu_item_id' => 'required|exists:menu_items,id',
                'quantity' => 'integer|min:1|max:10',
                'note' => 'nullable|string|max:200',
            ]);

            $result = $this->cartService->addToCart(
                $request->menu_item_id,
                $request->quantity ?? 1,
                $request->note ?? ''
            );

            return response()->json($result);
        } catch (\Exception $e) {
            \Log::error('Add to cart error: ' . $e->getMessage(), [
                'menu_item_id' => $request->menu_item_id ?? null,
                'exception' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Gagal menambahkan ke keranjang: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update cart item quantity (AJAX)
     */
    public function update(Request $request): JsonResponse
    {
        $request->validate([
            'menu_item_id' => 'required|exists:menu_items,id',
            'quantity' => 'required|integer|min:0|max:10',
        ]);

        $result = $this->cartService->updateQuantity(
            $request->menu_item_id,
            $request->quantity
        );

        return response()->json($result);
    }

    /**
     * Remove item from cart (AJAX)
     */
    public function remove(Request $request): JsonResponse
    {
        $request->validate([
            'menu_item_id' => 'required|exists:menu_items,id',
        ]);

        $result = $this->cartService->removeFromCart($request->menu_item_id);

        return response()->json($result);
    }

    /**
     * Clear cart
     */
    public function clear(): RedirectResponse
    {
        $this->cartService->clearCart();

        return redirect()->route('customer.cart.index')
            ->with('success', 'Keranjang berhasil dikosongkan');
    }

    /**
     * Get cart count (AJAX)
     */
    public function count(): JsonResponse
    {
        return response()->json([
            'count' => $this->cartService->getCartCount(),
            'total' => $this->cartService->getCartTotal(),
        ]);
    }
}
