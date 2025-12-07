<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CustomerSessionMiddlewareTest extends TestCase
{
    use RefreshDatabase;

    public function test_middleware_allows_access_with_customer_session()
    {
        $this->withSession([
            'customer_name' => 'John Doe',
            'customer_email' => 'john@example.com',
        ]);

        $response = $this->get(route('customer.home'));

        $response->assertStatus(200);
    }

    public function test_middleware_redirects_without_customer_name()
    {
        $this->withSession([
            'customer_email' => 'john@example.com',
        ]);

        $response = $this->get(route('customer.home'));

        $response->assertRedirect(route('login'));
    }

    public function test_middleware_redirects_without_customer_email()
    {
        $this->withSession([
            'customer_name' => 'John Doe',
        ]);

        $response = $this->get(route('customer.home'));

        $response->assertRedirect(route('login'));
    }

    public function test_middleware_allows_authenticated_customer_user()
    {
        $user = User::factory()->create(['role' => 'customer']);
        
        $this->withSession([
            'customer_name' => $user->name,
            'customer_email' => $user->email,
        ])->actingAs($user);

        $response = $this->get(route('customer.home'));

        $response->assertStatus(200);
    }

    public function test_middleware_blocks_guest_without_session()
    {
        $response = $this->get(route('customer.home'));

        $response->assertRedirect(route('login'));
    }

    public function test_cart_routes_are_protected()
    {
        $response = $this->get(route('customer.cart.index'));

        $response->assertRedirect(route('login'));
    }

    public function test_order_routes_are_protected()
    {
        $response = $this->get(route('customer.order.history'));

        $response->assertRedirect(route('login'));
    }

    public function test_menu_browsing_is_public()
    {
        $response = $this->get(route('landing.menu'));

        $response->assertStatus(200);
    }

    public function test_vibewall_is_public()
    {
        $response = $this->get(route('landing.vibewall'));

        $response->assertStatus(200);
    }
}
