<?php

namespace Tests\Unit;

use App\Models\FlashSale;
use App\Models\User;
use App\Services\FlashSaleService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FlashSaleServiceTest extends TestCase
{
    use RefreshDatabase;

    protected FlashSaleService $flashSaleService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->flashSaleService = app(FlashSaleService::class);
    }

    public function test_get_active_flash_sale()
    {
        FlashSale::factory()->create([
            'is_active' => true,
            'starts_at' => now()->subHour(),
            'ends_at' => now()->addHour(),
        ]);

        FlashSale::factory()->create([
            'is_active' => false,
        ]);

        $active = $this->flashSaleService->getActiveFlashSale();

        $this->assertNotNull($active);
        $this->assertTrue($active->is_active);
    }

    public function test_create_flash_sale()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $this->actingAs($admin);

        $flashSale = $this->flashSaleService->createFlashSale([
            'name' => 'Happy Hour Sale',
            'discount_percentage' => 30,
            'trigger_reason' => 'manual',
        ]);

        $this->assertEquals('Happy Hour Sale', $flashSale->name);
        $this->assertEquals(30, $flashSale->discount_percentage);
        $this->assertTrue($flashSale->is_active);
        $this->assertNotNull($flashSale->promo_code);
    }

    public function test_create_flash_sale_with_custom_promo_code()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $this->actingAs($admin);

        $flashSale = $this->flashSaleService->createFlashSale([
            'name' => 'Test Sale',
            'promo_code' => 'CUSTOM2024',
            'discount_percentage' => 25,
        ]);

        $this->assertEquals('CUSTOM2024', $flashSale->promo_code);
    }

    public function test_end_flash_sale()
    {
        $flashSale = FlashSale::factory()->create([
            'is_active' => true,
            'ends_at' => now()->addHours(2),
        ]);

        $ended = $this->flashSaleService->endFlashSale($flashSale->id);

        $this->assertFalse($ended->is_active);
        $this->assertTrue($ended->ends_at->isPast());
    }

    public function test_inactive_flash_sale_not_returned_as_active()
    {
        FlashSale::factory()->create([
            'is_active' => false,
            'starts_at' => now()->subHour(),
            'ends_at' => now()->addHour(),
        ]);

        $active = $this->flashSaleService->getActiveFlashSale();

        $this->assertNull($active);
    }

    public function test_expired_flash_sale_not_returned_as_active()
    {
        FlashSale::factory()->create([
            'is_active' => true,
            'starts_at' => now()->subHours(3),
            'ends_at' => now()->subHour(),
        ]);

        $active = $this->flashSaleService->getActiveFlashSale();

        $this->assertNull($active);
    }

    public function test_future_flash_sale_not_returned_as_active()
    {
        FlashSale::factory()->create([
            'is_active' => true,
            'starts_at' => now()->addHour(),
            'ends_at' => now()->addHours(3),
        ]);

        $active = $this->flashSaleService->getActiveFlashSale();

        $this->assertNull($active);
    }
}
