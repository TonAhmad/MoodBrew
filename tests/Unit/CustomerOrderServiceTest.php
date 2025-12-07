<?php

namespace Tests\Unit;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\MenuItem;
use App\Models\User;
use App\Services\CustomerOrderService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Session;
use Tests\TestCase;

class CustomerOrderServiceTest extends TestCase
{
    use RefreshDatabase;

    protected CustomerOrderService $orderService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->orderService = app(CustomerOrderService::class);
    }

    public function test_get_orders_for_authenticated_customer()
    {
        $user = User::factory()->create(['role' => 'customer']);
        
        $order = Order::factory()->create([
            'user_id' => $user->id,
        ]);

        $orders = $this->orderService->getMyOrders($user->id);

        $this->assertCount(1, $orders);
        $this->assertEquals($order->id, $orders->first()->id);
    }

    public function test_get_orders_for_guest_customer()
    {
        Session::put('customer_name', 'Guest User');
        Session::put('table_number', '5');

        $order = Order::factory()->create([
            'customer_name' => 'Guest User',
            'table_number' => '5',
            'created_at' => now(),
        ]);

        $orders = $this->orderService->getMyOrders(null, 'session-id');

        $this->assertCount(1, $orders);
        $this->assertEquals($order->order_number, $orders->first()->order_number);
    }

    public function test_get_order_by_number()
    {
        $order = Order::factory()->create([
            'order_number' => 'ORD-20240101-001',
        ]);

        $found = $this->orderService->getOrderByNumber('ORD-20240101-001');

        $this->assertNotNull($found);
        $this->assertEquals($order->id, $found->id);
    }

    public function test_get_active_orders_only()
    {
        $user = User::factory()->create(['role' => 'customer']);

        Order::factory()->create([
            'user_id' => $user->id,
            'status' => Order::STATUS_PENDING_PAYMENT,
        ]);

        Order::factory()->create([
            'user_id' => $user->id,
            'status' => Order::STATUS_PREPARING,
        ]);

        Order::factory()->create([
            'user_id' => $user->id,
            'status' => Order::STATUS_COMPLETED,
        ]);

        $activeOrders = $this->orderService->getActiveOrders($user->id);

        $this->assertCount(2, $activeOrders);
    }

    public function test_get_status_label_returns_correct_info()
    {
        $label = $this->orderService->getStatusLabel(Order::STATUS_PENDING_PAYMENT);

        $this->assertArrayHasKey('label', $label);
        $this->assertArrayHasKey('color', $label);
        $this->assertArrayHasKey('icon', $label);
        $this->assertEquals('Menunggu Pembayaran', $label['label']);
        $this->assertEquals('yellow', $label['color']);
    }

    public function test_get_preparing_status_label()
    {
        $label = $this->orderService->getStatusLabel(Order::STATUS_PREPARING);

        $this->assertEquals('Diproses', $label['label']);
        $this->assertEquals('blue', $label['color']);
    }

    public function test_orders_are_limited_to_10()
    {
        $user = User::factory()->create(['role' => 'customer']);

        Order::factory()->count(15)->create([
            'user_id' => $user->id,
        ]);

        $orders = $this->orderService->getMyOrders($user->id);

        $this->assertCount(10, $orders);
    }
}
