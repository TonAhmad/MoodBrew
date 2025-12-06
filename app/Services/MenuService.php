<?php

namespace App\Services;

use App\Models\MenuItem;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Str;

/**
 * MenuService - Business logic untuk manajemen menu
 * 
 * Menangani CRUD operations untuk menu items
 * Digunakan oleh Admin dan Cashier
 */
class MenuService
{
    /**
     * Get all menu items dengan pagination dan filter
     * 
     * @param int $perPage
     * @param string|null $search
     * @param string|null $category
     * @param bool|null $availability
     * @return LengthAwarePaginator
     */
    public function getAllMenuItems(
        int $perPage = 10,
        ?string $search = null,
        ?string $category = null,
        ?bool $availability = null
    ): LengthAwarePaginator {
        $query = MenuItem::query();

        // Search by name or description
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Filter by category
        if ($category) {
            $query->where('category', $category);
        }

        // Filter by availability
        if ($availability !== null) {
            $query->where('is_available', $availability);
        }

        return $query->latest()->paginate($perPage);
    }

    /**
     * Get menu item by ID
     * 
     * @param int $id
     * @return MenuItem|null
     */
    public function getMenuItemById(int $id): ?MenuItem
    {
        return MenuItem::find($id);
    }

    /**
     * Get menu items by category
     * 
     * @param string $category
     * @return Collection
     */
    public function getMenuItemsByCategory(string $category): Collection
    {
        return MenuItem::where('category', $category)
            ->where('is_available', true)
            ->get();
    }

    /**
     * Get available menu items
     * 
     * @return Collection
     */
    public function getAvailableMenuItems(): Collection
    {
        return MenuItem::available()->get();
    }

    /**
     * Create new menu item
     * 
     * @param array $data
     * @return array{success: bool, message: string, menuItem?: MenuItem}
     */
    public function createMenuItem(array $data): array
    {
        // Generate slug
        $slug = Str::slug($data['name']);

        // Check slug sudah ada
        if (MenuItem::where('slug', $slug)->exists()) {
            $slug = $slug . '-' . uniqid();
        }

        $menuItem = MenuItem::create([
            'name' => $data['name'],
            'slug' => $slug,
            'price' => $data['price'],
            'description' => $data['description'] ?? null,
            'category' => $data['category'],
            'stock_quantity' => $data['stock_quantity'] ?? 100,
            'is_available' => $data['is_available'] ?? true,
            'flavor_profile' => $this->parseFlavorProfile($data),
            'image_path' => $data['image_path'] ?? null,
        ]);

        return [
            'success' => true,
            'message' => 'Menu berhasil ditambahkan.',
            'menuItem' => $menuItem,
        ];
    }

    /**
     * Update menu item
     * 
     * @param int $id
     * @param array $data
     * @return array{success: bool, message: string, menuItem?: MenuItem}
     */
    public function updateMenuItem(int $id, array $data): array
    {
        $menuItem = $this->getMenuItemById($id);

        if (!$menuItem) {
            return [
                'success' => false,
                'message' => 'Menu tidak ditemukan.',
            ];
        }

        $updateData = [
            'name' => $data['name'] ?? $menuItem->name,
            'price' => $data['price'] ?? $menuItem->price,
            'description' => $data['description'] ?? $menuItem->description,
            'category' => $data['category'] ?? $menuItem->category,
            'stock_quantity' => $data['stock_quantity'] ?? $menuItem->stock_quantity,
            'is_available' => $data['is_available'] ?? $menuItem->is_available,
            'image_path' => $data['image_path'] ?? $menuItem->image_path,
        ];

        // Update slug jika nama berubah
        if (isset($data['name']) && $data['name'] !== $menuItem->name) {
            $slug = Str::slug($data['name']);
            if (MenuItem::where('slug', $slug)->where('id', '!=', $id)->exists()) {
                $slug = $slug . '-' . uniqid();
            }
            $updateData['slug'] = $slug;
        }

        // Update flavor profile jika ada
        if (isset($data['sweetness']) || isset($data['bitterness']) || isset($data['strength'])) {
            $updateData['flavor_profile'] = $this->parseFlavorProfile($data, $menuItem->flavor_profile);
        }

        $menuItem->update($updateData);

        return [
            'success' => true,
            'message' => 'Menu berhasil diperbarui.',
            'menuItem' => $menuItem->fresh(),
        ];
    }

    /**
     * Delete menu item
     * 
     * @param int $id
     * @return array{success: bool, message: string}
     */
    public function deleteMenuItem(int $id): array
    {
        $menuItem = $this->getMenuItemById($id);

        if (!$menuItem) {
            return [
                'success' => false,
                'message' => 'Menu tidak ditemukan.',
            ];
        }

        // Check if menu has orders
        if ($menuItem->orderItems()->exists()) {
            // Soft approach: just mark as unavailable instead of delete
            $menuItem->update(['is_available' => false]);
            return [
                'success' => true,
                'message' => 'Menu telah dinonaktifkan karena sudah pernah dipesan.',
            ];
        }

        $menuItem->delete();

        return [
            'success' => true,
            'message' => 'Menu berhasil dihapus.',
        ];
    }

    /**
     * Toggle menu availability
     * 
     * @param int $id
     * @return array{success: bool, message: string, menuItem?: MenuItem}
     */
    public function toggleAvailability(int $id): array
    {
        $menuItem = $this->getMenuItemById($id);

        if (!$menuItem) {
            return [
                'success' => false,
                'message' => 'Menu tidak ditemukan.',
            ];
        }

        $menuItem->update(['is_available' => !$menuItem->is_available]);

        $status = $menuItem->is_available ? 'tersedia' : 'tidak tersedia';

        return [
            'success' => true,
            'message' => "Menu {$menuItem->name} sekarang {$status}.",
            'menuItem' => $menuItem,
        ];
    }

    /**
     * Get menu statistics
     * 
     * @return array
     */
    public function getMenuStats(): array
    {
        return [
            'totalItems' => MenuItem::count(),
            'availableItems' => MenuItem::where('is_available', true)->count(),
            'coffeeItems' => MenuItem::where('category', MenuItem::CATEGORY_COFFEE)->count(),
            'nonCoffeeItems' => MenuItem::where('category', MenuItem::CATEGORY_NON_COFFEE)->count(),
            'pastryItems' => MenuItem::where('category', MenuItem::CATEGORY_PASTRY)->count(),
            'mainCourseItems' => MenuItem::where('category', MenuItem::CATEGORY_MAIN_COURSE)->count(),
        ];
    }

    /**
     * Get all categories
     * 
     * @return array
     */
    public function getCategories(): array
    {
        return [
            MenuItem::CATEGORY_COFFEE => 'Coffee',
            MenuItem::CATEGORY_NON_COFFEE => 'Non-Coffee',
            MenuItem::CATEGORY_PASTRY => 'Pastry',
            MenuItem::CATEGORY_MAIN_COURSE => 'Main Course',
        ];
    }

    /**
     * Parse flavor profile from form data
     * 
     * @param array $data
     * @param array|null $existing
     * @return array
     */
    private function parseFlavorProfile(array $data, ?array $existing = null): array
    {
        $profile = $existing ?? [
            'sweetness' => 'medium',
            'bitterness' => 'medium',
            'strength' => 'medium',
            'notes' => [],
        ];

        if (isset($data['sweetness'])) {
            $profile['sweetness'] = $data['sweetness'];
        }
        if (isset($data['bitterness'])) {
            $profile['bitterness'] = $data['bitterness'];
        }
        if (isset($data['strength'])) {
            $profile['strength'] = $data['strength'];
        }
        if (isset($data['flavor_notes'])) {
            $profile['notes'] = is_array($data['flavor_notes'])
                ? $data['flavor_notes']
                : array_map('trim', explode(',', $data['flavor_notes']));
        }

        return $profile;
    }
}
