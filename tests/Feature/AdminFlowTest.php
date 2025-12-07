<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\MenuItem;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AdminFlowTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->admin = User::factory()->create([
            'email' => 'admin@moodbrew.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
        ]);
    }

    public function test_admin_can_login()
    {
        $response = $this->post('/staff/login', [
            'email' => 'admin@moodbrew.com',
            'password' => 'password',
        ]);

        $response->assertRedirect(route('admin.dashboard'));
        $this->assertAuthenticatedAs($this->admin);
    }

    public function test_admin_can_access_dashboard()
    {
        $this->actingAs($this->admin);

        $response = $this->get(route('admin.dashboard'));

        $response->assertStatus(200);
        $response->assertSee('Dashboard');
    }

    public function test_admin_can_create_menu_item()
    {
        $this->actingAs($this->admin);

        $response = $this->post(route('admin.menu.store'), [
            'name' => 'New Coffee',
            'description' => 'Delicious coffee',
            'price' => 40000,
            'category' => 'coffee',
            'is_available' => true,
            'stock_quantity' => 50,
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('menu_items', [
            'name' => 'New Coffee',
            'price' => 40000,
        ]);
    }

    public function test_admin_can_update_menu_item()
    {
        $this->actingAs($this->admin);
        
        $menuItem = MenuItem::factory()->create(['price' => 30000]);

        $response = $this->put(route('admin.menu.update', $menuItem), [
            'name' => $menuItem->name,
            'description' => $menuItem->description,
            'price' => 35000,
            'category' => $menuItem->category,
            'is_available' => $menuItem->is_available,
            'stock_quantity' => $menuItem->stock_quantity,
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('menu_items', [
            'id' => $menuItem->id,
            'price' => 35000,
        ]);
    }

    public function test_admin_can_delete_menu_item()
    {
        $this->actingAs($this->admin);
        
        $menuItem = MenuItem::factory()->create();

        $response = $this->delete(route('admin.menu.destroy', $menuItem));

        $response->assertRedirect();
        $this->assertDatabaseMissing('menu_items', ['id' => $menuItem->id]);
    }

    public function test_non_admin_cannot_access_admin_routes()
    {
        $cashier = User::factory()->create(['role' => 'cashier']);
        $this->actingAs($cashier);

        $response = $this->get(route('admin.dashboard'));

        $response->assertRedirect();
    }

    public function test_guest_cannot_access_admin_routes()
    {
        $response = $this->get(route('admin.dashboard'));

        $response->assertRedirect(route('login'));
    }
}
