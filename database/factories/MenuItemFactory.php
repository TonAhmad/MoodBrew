<?php

namespace Database\Factories;

use App\Models\MenuItem;
use Illuminate\Database\Eloquent\Factories\Factory;

class MenuItemFactory extends Factory
{
    protected $model = MenuItem::class;

    public function definition(): array
    {
        return [
            'name' => fake()->words(2, true),
            'slug' => fake()->slug(),
            'description' => fake()->sentence(),
            'price' => fake()->numberBetween(15000, 50000),
            'category' => fake()->randomElement(['coffee', 'non-coffee', 'snack', 'pastry']),
            'is_available' => true,
            'stock_quantity' => fake()->numberBetween(10, 100),
            'image_path' => null,
        ];
    }
}
