<?php

namespace Tests\Unit;

use App\Models\MenuItem;
use App\Services\MenuService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MenuServiceTest extends TestCase
{
    use RefreshDatabase;

    protected MenuService $menuService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->menuService = app(MenuService::class);
    }

    public function test_get_all_menu_items()
    {
        MenuItem::factory()->count(5)->create();

        $items = $this->menuService->getAllMenuItems();

        $this->assertCount(5, $items);
    }

    public function test_get_available_menu_items_only()
    {
        MenuItem::factory()->count(3)->create(['is_available' => true]);
        MenuItem::factory()->count(2)->create(['is_available' => false]);

        $items = $this->menuService->getAvailableMenuItems();

        $this->assertCount(3, $items);
    }

    public function test_get_menu_item_by_id()
    {
        $menuItem = MenuItem::factory()->create(['name' => 'Espresso']);

        $found = $this->menuService->getMenuItemById($menuItem->id);

        $this->assertEquals('Espresso', $found->name);
    }

    public function test_create_menu_item()
    {
        $data = [
            'name' => 'Latte',
            'description' => 'Smooth and creamy',
            'price' => 35000,
            'category' => 'coffee',
            'is_available' => true,
            'stock_quantity' => 20,
        ];

        $result = $this->menuService->createMenuItem($data);

        $this->assertTrue($result['success']);
        $this->assertArrayHasKey('menuItem', $result);
        $this->assertEquals('Latte', $result['menuItem']->name);
        $this->assertDatabaseHas('menu_items', ['name' => 'Latte']);
    }

    public function test_update_menu_item()
    {
        $menuItem = MenuItem::factory()->create(['price' => 30000]);

        $updated = $this->menuService->updateMenuItem($menuItem->id, [
            'price' => 35000,
        ]);

        $this->assertEquals(35000, $updated->price);
    }

    public function test_toggle_availability()
    {
        $menuItem = MenuItem::factory()->create(['is_available' => true]);

        $toggled = $this->menuService->toggleAvailability($menuItem->id);

        $this->assertFalse($toggled->is_available);

        $toggledAgain = $this->menuService->toggleAvailability($menuItem->id);
        $this->assertTrue($toggledAgain->is_available);
    }

    public function test_delete_menu_item()
    {
        $menuItem = MenuItem::factory()->create();

        $this->menuService->deleteMenuItem($menuItem->id);

        $this->assertDatabaseMissing('menu_items', ['id' => $menuItem->id]);
    }

    public function test_get_menu_by_category()
    {
        MenuItem::factory()->count(3)->create(['category' => 'coffee']);
        MenuItem::factory()->count(2)->create(['category' => 'non-coffee']);

        $menuByCategory = $this->menuService->getMenuByCategory();

        $this->assertArrayHasKey('coffee', $menuByCategory);
        $this->assertArrayHasKey('non-coffee', $menuByCategory);
        $this->assertCount(3, $menuByCategory['coffee']);
        $this->assertCount(2, $menuByCategory['non-coffee']);
    }
}
