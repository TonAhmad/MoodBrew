<?php

namespace Tests\Unit;

use App\Models\MenuItem;
use App\Services\CartService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Session;
use Tests\TestCase;

class CartServiceTest extends TestCase
{
    use RefreshDatabase;

    protected CartService $cartService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->cartService = app(CartService::class);
        Session::forget('cart');
    }

    public function test_add_item_to_empty_cart()
    {
        $menuItem = MenuItem::factory()->create([
            'price' => 25000,
            'is_available' => true,
            'stock_quantity' => 10,
        ]);

        $result = $this->cartService->addItem($menuItem->id, 2);

        $this->assertTrue($result['success']);
        $this->assertEquals(2, $result['cart_count']);
        
        $cart = Session::get('cart');
        $this->assertCount(1, $cart);
        $this->assertEquals(2, $cart[0]['quantity']);
        $this->assertEquals($menuItem->id, $cart[0]['menu_item_id']);
    }

    public function test_add_existing_item_increases_quantity()
    {
        $menuItem = MenuItem::factory()->create([
            'is_available' => true,
            'stock_quantity' => 10,
        ]);

        $this->cartService->addItem($menuItem->id, 1);
        $result = $this->cartService->addItem($menuItem->id, 2);

        $this->assertTrue($result['success']);
        $cart = Session::get('cart');
        $this->assertEquals(3, $cart[0]['quantity']);
    }

    public function test_cannot_add_unavailable_item()
    {
        $menuItem = MenuItem::factory()->create([
            'is_available' => false,
        ]);

        $result = $this->cartService->addItem($menuItem->id, 1);

        $this->assertFalse($result['success']);
        $this->assertStringContainsString('tidak tersedia', strtolower($result['message']));
    }

    public function test_cannot_add_more_than_stock()
    {
        $menuItem = MenuItem::factory()->create([
            'is_available' => true,
            'stock_quantity' => 5,
        ]);

        $result = $this->cartService->addItem($menuItem->id, 10);

        $this->assertFalse($result['success']);
        $this->assertStringContainsString('stok', strtolower($result['message']));
    }

    public function test_update_item_quantity()
    {
        $menuItem = MenuItem::factory()->create([
            'is_available' => true,
            'stock_quantity' => 10,
        ]);

        $this->cartService->addItem($menuItem->id, 2);
        $result = $this->cartService->updateQuantity($menuItem->id, 5);

        $this->assertTrue($result['success']);
        $cart = Session::get('cart');
        $this->assertEquals(5, $cart[0]['quantity']);
    }

    public function test_remove_item_from_cart()
    {
        $menuItem = MenuItem::factory()->create();
        $this->cartService->addItem($menuItem->id, 2);

        $result = $this->cartService->removeItem($menuItem->id);

        $this->assertTrue($result['success']);
        $this->assertEmpty(Session::get('cart', []));
    }

    public function test_clear_cart()
    {
        $menuItem1 = MenuItem::factory()->create();
        $menuItem2 = MenuItem::factory()->create();
        
        $this->cartService->addItem($menuItem1->id, 1);
        $this->cartService->addItem($menuItem2->id, 2);

        $this->cartService->clearCart();

        $this->assertEmpty(Session::get('cart', []));
    }

    public function test_get_cart_items_with_details()
    {
        $menuItem = MenuItem::factory()->create([
            'name' => 'Cappuccino',
            'price' => 30000,
        ]);

        $this->cartService->addItem($menuItem->id, 2);
        $items = $this->cartService->getCartItems();

        $this->assertCount(1, $items);
        $this->assertEquals('Cappuccino', $items[0]['menu_item']->name);
        $this->assertEquals(2, $items[0]['quantity']);
        $this->assertEquals(60000, $items[0]['subtotal']);
    }

    public function test_get_cart_total()
    {
        $menuItem1 = MenuItem::factory()->create(['price' => 25000]);
        $menuItem2 = MenuItem::factory()->create(['price' => 30000]);

        $this->cartService->addItem($menuItem1->id, 2);
        $this->cartService->addItem($menuItem2->id, 1);

        $total = $this->cartService->getCartTotal();

        $this->assertEquals(80000, $total);
    }

    public function test_get_cart_count()
    {
        $menuItem1 = MenuItem::factory()->create();
        $menuItem2 = MenuItem::factory()->create();

        $this->cartService->addItem($menuItem1->id, 2);
        $this->cartService->addItem($menuItem2->id, 3);

        $count = $this->cartService->getCartCount();

        $this->assertEquals(5, $count);
    }
}
