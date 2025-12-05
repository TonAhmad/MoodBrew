<?php

namespace App\Services;

use App\Models\FlashSale;
use App\Models\MenuItem;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * CartService - Business logic untuk shopping cart customer
 * 
 * Cart disimpan di session untuk guest dan customer
 * Format session('cart'):
 * [
 *     'menu_item_id' => [
 *         'quantity' => int,
 *         'note' => string,
 *         'price' => float (snapshot saat add)
 *     ]
 * ]
 */
class CartService
{
    /**
     * Get cart items with full menu data
     */
    public function getCartItems(): Collection
    {
        $cart = session('cart', []);
        
        if (empty($cart)) {
            return collect([]);
        }

        $menuIds = array_keys($cart);
        $menuItems = MenuItem::whereIn('id', $menuIds)->get()->keyBy('id');

        return collect($cart)->map(function ($item, $menuId) use ($menuItems) {
            $menu = $menuItems->get($menuId);
            
            if (!$menu || !$menu->is_available) {
                return null;
            }

            // Check flash sale price
            $currentPrice = $this->getCurrentPrice($menu);

            return [
                'menu_item_id' => $menuId,
                'menu_item' => $menu,
                'quantity' => $item['quantity'],
                'note' => $item['note'] ?? '',
                'price_at_moment' => $currentPrice,
                'subtotal' => $currentPrice * $item['quantity'],
            ];
        })->filter()->values();
    }

    /**
     * Add item to cart
     */
    public function addToCart(int $menuItemId, int $quantity = 1, string $note = ''): array
    {
        try {
            $menu = MenuItem::find($menuItemId);

            if (!$menu || !$menu->is_available) {
                return ['success' => false, 'message' => 'Menu tidak tersedia'];
            }

            if ($menu->stock_quantity < $quantity) {
                return ['success' => false, 'message' => 'Stok tidak mencukupi'];
            }

            $cart = session('cart', []);
            
            if (isset($cart[$menuItemId])) {
                $cart[$menuItemId]['quantity'] += $quantity;
                if ($note) {
                    $cart[$menuItemId]['note'] = $note;
                }
            } else {
                $cart[$menuItemId] = [
                    'quantity' => $quantity,
                    'note' => $note,
                    'price' => $this->getCurrentPrice($menu),
                ];
            }

            session(['cart' => $cart]);

            return [
                'success' => true,
                'message' => "{$menu->name} ditambahkan ke keranjang",
                'cart_count' => $this->getCartCount(),
            ];
        } catch (\Exception $e) {
            \Log::error('CartService addToCart error: ' . $e->getMessage(), [
                'menu_item_id' => $menuItemId,
                'trace' => $e->getTraceAsString()
            ]);
            
            return [
                'success' => false,
                'message' => 'Terjadi kesalahan saat menambahkan ke keranjang'
            ];
        }
    }    /**
     * Update cart item quantity
     */
    public function updateQuantity(int $menuItemId, int $quantity): array
    {
        $cart = session('cart', []);

        if (!isset($cart[$menuItemId])) {
            return ['success' => false, 'message' => 'Item tidak ditemukan di keranjang'];
        }

        if ($quantity <= 0) {
            return $this->removeFromCart($menuItemId);
        }

        $menu = MenuItem::find($menuItemId);
        if ($menu && $menu->stock_quantity < $quantity) {
            return ['success' => false, 'message' => 'Stok tidak mencukupi'];
        }

        $cart[$menuItemId]['quantity'] = $quantity;
        session(['cart' => $cart]);

        return [
            'success' => true,
            'message' => 'Jumlah diupdate',
            'cart_count' => $this->getCartCount(),
        ];
    }

    /**
     * Remove item from cart
     */
    public function removeFromCart(int $menuItemId): array
    {
        $cart = session('cart', []);
        
        if (isset($cart[$menuItemId])) {
            unset($cart[$menuItemId]);
            session(['cart' => $cart]);
        }

        return [
            'success' => true,
            'message' => 'Item dihapus dari keranjang',
            'cart_count' => $this->getCartCount(),
        ];
    }

    /**
     * Clear cart
     */
    public function clearCart(): void
    {
        session()->forget('cart');
    }

    /**
     * Get cart count
     */
    public function getCartCount(): int
    {
        $cart = session('cart', []);
        return array_sum(array_column($cart, 'quantity'));
    }

    /**
     * Get cart total
     */
    public function getCartTotal(): float
    {
        return $this->getCartItems()->sum('subtotal');
    }

    /**
     * Create order from cart
     */
    public function createOrder(array $orderData): array
    {
        $cartItems = $this->getCartItems();
        
        if ($cartItems->isEmpty()) {
            return ['success' => false, 'message' => 'Keranjang kosong'];
        }

        try {
            DB::beginTransaction();

            // Generate order number
            $orderNumber = 'ORD-' . strtoupper(Str::random(8));

            // Create order
            $order = Order::create([
                'order_number' => $orderNumber,
                'table_number' => $orderData['table_number'] ?? session('table_number'),
                'customer_name' => $orderData['customer_name'] ?? session('customer_name', 'Guest'),
                'total_amount' => $this->getCartTotal(),
                'status' => Order::STATUS_PENDING_PAYMENT,
                'notes' => $orderData['notes'] ?? null,
                'customer_mood_summary' => $orderData['mood_summary'] ?? null,
                'user_id' => auth()->id(),
            ]);

            // Create order items
            foreach ($cartItems as $item) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'menu_item_id' => $item['menu_item_id'],
                    'quantity' => $item['quantity'],
                    'price_at_moment' => $item['price_at_moment'],
                    'note' => $item['note'],
                ]);

                // Update stock
                $menuItem = MenuItem::find($item['menu_item_id']);
                if ($menuItem) {
                    $menuItem->decrement('stock_quantity', $item['quantity']);
                }
            }

            DB::commit();

            // Clear cart
            $this->clearCart();

            return [
                'success' => true,
                'message' => 'Pesanan berhasil dibuat!',
                'order' => $order,
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            return ['success' => false, 'message' => 'Gagal membuat pesanan: ' . $e->getMessage()];
        }
    }

    /**
     * Get current price (considering flash sale)
     */
    private function getCurrentPrice(MenuItem $menu): float
    {
        try {
            // Get active global flash sale (promo applies to all items)
            $flashSale = FlashSale::query()
                ->where('is_active', true)
                ->where('starts_at', '<=', now())
                ->where('ends_at', '>=', now())
                ->first();

            if ($flashSale && $flashSale->discount_percentage > 0) {
                // Apply discount percentage to menu price
                $discount = ($menu->price * $flashSale->discount_percentage) / 100;
                return round($menu->price - $discount, 2);
            }

            return $menu->price;
        } catch (\Exception $e) {
            \Log::warning('Error getting flash sale price, using regular price: ' . $e->getMessage());
            return $menu->price;
        }
    }
}
