<?php

namespace Database\Seeders;

use App\Models\MenuItem;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

/**
 * MenuItemSeeder - Seed sample menu items
 * 
 * Creates menu items with flavor_profile untuk AI recommendation
 */
class MenuItemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $menuItems = [
            // Coffee
            [
                'name' => 'Espresso Shot',
                'price' => 18000,
                'description' => 'Pure espresso dengan crema yang tebal. Intense dan bold.',
                'category' => 'coffee',
                'stock_quantity' => 100,
                'flavor_profile' => [
                    'acidity' => 3,
                    'body' => 5,
                    'sweetness' => 1,
                    'mood_tags' => ['energizing', 'bold', 'morning-boost', 'focused'],
                ],
            ],
            [
                'name' => 'Cappuccino',
                'price' => 28000,
                'description' => 'Espresso dengan steamed milk dan foam yang lembut.',
                'category' => 'coffee',
                'stock_quantity' => 100,
                'flavor_profile' => [
                    'acidity' => 2,
                    'body' => 3,
                    'sweetness' => 2,
                    'mood_tags' => ['comforting', 'classic', 'balanced', 'relaxing'],
                ],
            ],
            [
                'name' => 'Caramel Latte',
                'price' => 35000,
                'description' => 'Espresso dengan susu dan sirup caramel manis.',
                'category' => 'coffee',
                'stock_quantity' => 100,
                'flavor_profile' => [
                    'acidity' => 1,
                    'body' => 3,
                    'sweetness' => 4,
                    'mood_tags' => ['sweet', 'comforting', 'treat-yourself', 'happy'],
                ],
            ],
            [
                'name' => 'Americano',
                'price' => 22000,
                'description' => 'Espresso dengan air panas. Clean dan refreshing.',
                'category' => 'coffee',
                'stock_quantity' => 100,
                'flavor_profile' => [
                    'acidity' => 3,
                    'body' => 2,
                    'sweetness' => 1,
                    'mood_tags' => ['focused', 'clean', 'productive', 'morning'],
                ],
            ],
            [
                'name' => 'Mocha',
                'price' => 38000,
                'description' => 'Espresso dengan cokelat dan steamed milk. Decadent!',
                'category' => 'coffee',
                'stock_quantity' => 100,
                'flavor_profile' => [
                    'acidity' => 1,
                    'body' => 4,
                    'sweetness' => 5,
                    'mood_tags' => ['indulgent', 'sweet', 'comfort-food', 'stress-relief'],
                ],
            ],

            // Non-Coffee
            [
                'name' => 'Matcha Latte',
                'price' => 32000,
                'description' => 'Premium matcha dengan susu. Earthy dan creamy.',
                'category' => 'non-coffee',
                'stock_quantity' => 80,
                'flavor_profile' => [
                    'acidity' => 1,
                    'body' => 3,
                    'sweetness' => 2,
                    'mood_tags' => ['calm', 'zen', 'healthy', 'mindful', 'relaxing'],
                ],
            ],
            [
                'name' => 'Fresh Orange Juice',
                'price' => 25000,
                'description' => 'Jus jeruk segar tanpa gula tambahan.',
                'category' => 'non-coffee',
                'stock_quantity' => 50,
                'flavor_profile' => [
                    'acidity' => 4,
                    'body' => 1,
                    'sweetness' => 3,
                    'mood_tags' => ['refreshing', 'healthy', 'energizing', 'bright'],
                ],
            ],
            [
                'name' => 'Hot Chocolate',
                'price' => 28000,
                'description' => 'Cokelat hangat yang rich dan creamy.',
                'category' => 'non-coffee',
                'stock_quantity' => 100,
                'flavor_profile' => [
                    'acidity' => 1,
                    'body' => 4,
                    'sweetness' => 5,
                    'mood_tags' => ['comforting', 'nostalgic', 'cozy', 'happy', 'stress-relief'],
                ],
            ],

            // Pastry
            [
                'name' => 'Croissant Butter',
                'price' => 22000,
                'description' => 'Croissant klasik dengan lapisan butter yang flaky.',
                'category' => 'pastry',
                'stock_quantity' => 30,
                'flavor_profile' => [
                    'mood_tags' => ['indulgent', 'classic', 'breakfast', 'treat'],
                ],
            ],
            [
                'name' => 'Chocolate Muffin',
                'price' => 18000,
                'description' => 'Muffin cokelat dengan chocolate chips.',
                'category' => 'pastry',
                'stock_quantity' => 25,
                'flavor_profile' => [
                    'mood_tags' => ['sweet', 'comfort-food', 'snack', 'happy'],
                ],
            ],

            // Main Course
            [
                'name' => 'Chicken Sandwich',
                'price' => 45000,
                'description' => 'Sandwich dengan ayam panggang, lettuce, dan mayo.',
                'category' => 'main_course',
                'stock_quantity' => 20,
                'flavor_profile' => [
                    'mood_tags' => ['filling', 'savory', 'lunch', 'satisfying'],
                ],
            ],
            [
                'name' => 'Pasta Aglio Olio',
                'price' => 48000,
                'description' => 'Spaghetti dengan garlic, olive oil, dan chili flakes.',
                'category' => 'main_course',
                'stock_quantity' => 15,
                'flavor_profile' => [
                    'mood_tags' => ['savory', 'filling', 'comfort-food', 'lunch'],
                ],
            ],
        ];

        foreach ($menuItems as $item) {
            MenuItem::create([
                'name' => $item['name'],
                'slug' => Str::slug($item['name']),
                'price' => $item['price'],
                'description' => $item['description'],
                'category' => $item['category'],
                'stock_quantity' => $item['stock_quantity'],
                'is_available' => true,
                'flavor_profile' => $item['flavor_profile'],
            ]);
        }
    }
}
