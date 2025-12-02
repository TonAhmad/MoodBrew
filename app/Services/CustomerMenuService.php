<?php

namespace App\Services;

use App\Models\FlashSale;
use App\Models\MenuItem;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

/**
 * CustomerMenuService - Business logic untuk menu browsing customer
 * 
 * Service ini menangani:
 * - Menampilkan menu dengan filter kategori dan mood
 * - Mengecek flash sale aktif
 * - Rekomendasi berdasarkan mood tags
 */
class CustomerMenuService
{
    /**
     * Get all available menu items with filters
     */
    public function getMenuItems(array $filters = []): LengthAwarePaginator
    {
        $query = MenuItem::query()
            ->where('is_available', true)
            ->where('stock_quantity', '>', 0);

        // Filter by category
        if (!empty($filters['category'])) {
            $query->where('category', $filters['category']);
        }

        // Filter by mood tag (stored inside flavor_profile JSON)
        if (!empty($filters['mood'])) {
            $query->whereJsonContains('flavor_profile->mood_tags', $filters['mood']);
        }

        // Search by name
        if (!empty($filters['search'])) {
            $query->where('name', 'like', '%' . $filters['search'] . '%');
        }

        // Sort options
        $sortBy = $filters['sort'] ?? 'name';
        $sortDir = $filters['direction'] ?? 'asc';
        
        if ($sortBy === 'price') {
            $query->orderBy('price', $sortDir);
        } elseif ($sortBy === 'popularity') {
            $query->withCount('orderItems')->orderBy('order_items_count', 'desc');
        } else {
            $query->orderBy('name', 'asc');
        }

        return $query->paginate(12);
    }

    /**
     * Get menu item by slug
     */
    public function getMenuItem(string $slug): ?MenuItem
    {
        return MenuItem::where('slug', $slug)
            ->where('is_available', true)
            ->first();
    }

    /**
     * Get categories with item counts
     */
    public function getCategories(): Collection
    {
        return MenuItem::query()
            ->where('is_available', true)
            ->selectRaw('category, COUNT(*) as count')
            ->groupBy('category')
            ->get()
            ->mapWithKeys(function ($item) {
                return [$item->category => [
                    'name' => $this->getCategoryLabel($item->category),
                    'count' => $item->count,
                    'icon' => $this->getCategoryIcon($item->category),
                ]];
            });
    }

    /**
     * Get active flash sales (global promos)
     */
    public function getActiveFlashSales(): Collection
    {
        return FlashSale::query()
            ->where('is_active', true)
            ->where('starts_at', '<=', now())
            ->where('ends_at', '>=', now())
            ->get();
    }

    /**
     * Get current active flash sale (for applying discount)
     */
    public function getCurrentFlashSale(): ?FlashSale
    {
        return FlashSale::query()
            ->where('is_active', true)
            ->where('starts_at', '<=', now())
            ->where('ends_at', '>=', now())
            ->first();
    }

    /**
     * Check if there's an active flash sale and calculate discounted price
     */
    public function getFlashSalePrice(MenuItem $menuItem): ?array
    {
        $flashSale = $this->getCurrentFlashSale();

        if (!$flashSale) {
            return null;
        }

        $discountedPrice = $flashSale->calculateFinalPrice($menuItem->price);

        return [
            'original_price' => $menuItem->price,
            'sale_price' => $discountedPrice,
            'discount_percent' => $flashSale->discount_percentage,
            'promo_name' => $flashSale->name,
            'promo_code' => $flashSale->promo_code,
            'ends_at' => $flashSale->ends_at,
        ];
    }

    /**
     * Get mood-based recommendations (non-AI fallback)
     */
    public function getMoodBasedItems(string $mood, int $limit = 4): Collection
    {
        return MenuItem::query()
            ->where('is_available', true)
            ->where('stock_quantity', '>', 0)
            ->whereJsonContains('flavor_profile->mood_tags', $mood)
            ->limit($limit)
            ->get();
    }

    /**
     * Get popular items
     */
    public function getPopularItems(int $limit = 6): Collection
    {
        return MenuItem::query()
            ->where('is_available', true)
            ->withCount('orderItems')
            ->orderBy('order_items_count', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Get category label
     */
    private function getCategoryLabel(string $category): string
    {
        return match ($category) {
            MenuItem::CATEGORY_COFFEE => 'Kopi',
            MenuItem::CATEGORY_NON_COFFEE => 'Non-Kopi',
            MenuItem::CATEGORY_PASTRY => 'Pastry',
            MenuItem::CATEGORY_MAIN_COURSE => 'Makanan',
            default => ucfirst($category),
        };
    }

    /**
     * Get category icon
     */
    private function getCategoryIcon(string $category): string
    {
        return match ($category) {
            MenuItem::CATEGORY_COFFEE => '☕',
            MenuItem::CATEGORY_NON_COFFEE => '🧃',
            MenuItem::CATEGORY_PASTRY => '🥐',
            MenuItem::CATEGORY_MAIN_COURSE => '🍽️',
            default => '📦',
        };
    }
}
