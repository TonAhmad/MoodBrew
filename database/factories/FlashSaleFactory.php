<?php

namespace Database\Factories;

use App\Models\FlashSale;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class FlashSaleFactory extends Factory
{
    protected $model = FlashSale::class;

    public function definition(): array
    {
        return [
            'name' => fake()->words(3, true) . ' Sale',
            'promo_code' => strtoupper(fake()->lexify('????')) . fake()->numerify('##'),
            'discount_percentage' => fake()->numberBetween(10, 50),
            'trigger_reason' => fake()->randomElement(['manual', 'dead_hour', 'promotion']),
            'triggered_by' => User::factory(),
            'starts_at' => now(),
            'ends_at' => now()->addHours(2),
            'is_active' => true,
            'ai_generated_copy' => json_encode([
                'headline' => fake()->sentence(),
                'description' => fake()->paragraph(),
            ]),
        ];
    }
}
