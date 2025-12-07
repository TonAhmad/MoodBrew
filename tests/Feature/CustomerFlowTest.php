<?php

namespace Tests\Feature;

use App\Models\MenuItem;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CustomerFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_customer_can_access_landing_page()
    {
        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertSee('MoodBrew');
        $response->assertSee('Cafe yang');
    }

    public function test_customer_can_view_login_page()
    {
        $response = $this->get('/login');

        $response->assertStatus(200);
        $response->assertSee('Mulai Pesan');
    }

    public function test_customer_can_login_with_quick_access()
    {
        $response = $this->post('/login', [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'table_number' => '5',
        ]);

        $response->assertRedirect(route('customer.home'));
        $this->assertEquals('John Doe', session('customer_name'));
        $this->assertEquals('john@example.com', session('customer_email'));
        $this->assertEquals('5', session('table_number'));
    }

    public function test_customer_cannot_access_protected_routes_without_login()
    {
        $response = $this->get(route('customer.home'));

        $response->assertRedirect(route('login'));
    }

    public function test_customer_can_view_menu_after_login()
    {
        MenuItem::factory()->create(['name' => 'Cappuccino', 'is_available' => true]);

        $this->withSession([
            'customer_name' => 'John Doe',
            'customer_email' => 'john@example.com',
        ]);

        $response = $this->get(route('customer.menu.index'));

        $response->assertStatus(200);
        $response->assertSee('Cappuccino');
    }

    public function test_customer_can_add_item_to_cart()
    {
        $menuItem = MenuItem::factory()->create([
            'is_available' => true,
            'stock_quantity' => 10,
        ]);

        $this->withSession([
            'customer_name' => 'John Doe',
            'customer_email' => 'john@example.com',
        ]);

        $response = $this->post(route('customer.cart.add'), [
            'menu_item_id' => $menuItem->id,
            'quantity' => 2,
        ]);

        $response->assertJson(['success' => true]);
        $this->assertNotEmpty(session('cart'));
    }

    public function test_customer_can_view_cart()
    {
        $menuItem = MenuItem::factory()->create(['name' => 'Espresso']);

        $this->withSession([
            'customer_name' => 'John Doe',
            'customer_email' => 'john@example.com',
            'cart' => [
                [
                    'menu_item_id' => $menuItem->id,
                    'quantity' => 2,
                ],
            ],
        ]);

        $response = $this->get(route('customer.cart.index'));

        $response->assertStatus(200);
        $response->assertSee('Espresso');
    }

    public function test_customer_can_logout()
    {
        $this->withSession([
            'customer_name' => 'John Doe',
            'customer_email' => 'john@example.com',
        ]);

        $response = $this->post(route('logout'));

        $response->assertRedirect(route('landing.home'));
        $this->assertNull(session('customer_name'));
    }

    public function test_public_menu_page_is_accessible_without_login()
    {
        MenuItem::factory()->create(['name' => 'Latte']);

        $response = $this->get(route('landing.menu'));

        $response->assertStatus(200);
        $response->assertSee('Latte');
    }

    public function test_public_vibewall_page_is_accessible_without_login()
    {
        $response = $this->get(route('landing.vibewall'));

        $response->assertStatus(200);
        $response->assertSee('Vibe Wall');
    }
}
