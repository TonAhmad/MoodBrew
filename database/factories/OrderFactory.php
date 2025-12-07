<?php

namespace Database\Factories;

use App\Models\Order;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class OrderFactory extends Factory
{
    protected $model = Order::class;

    public function definition(): array
    {
        return [
            'order_number' => 'ORD-' . now()->format('Ymd') . '-' . str_pad(rand(1, 999), 3, '0', STR_PAD_LEFT),
            'user_id' => null,
            'customer_name' => fake()->name(),
            'table_number' => (string) fake()->numberBetween(1, 20),
            'total_amount' => fake()->numberBetween(20000, 200000),
            'status' => fake()->randomElement([
                Order::STATUS_PENDING_PAYMENT,
                Order::STATUS_PREPARING,
                Order::STATUS_READY,
                Order::STATUS_COMPLETED,
                Order::STATUS_CANCELLED,
            ]),
            'notes' => fake()->optional()->sentence(),
            'payment_method' => fake()->randomElement(['cash', 'qris', 'debit']),
            'paid_at' => null,
        ];
    }
}
