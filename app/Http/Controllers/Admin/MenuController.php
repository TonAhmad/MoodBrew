<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\MenuItemRequest;
use App\Models\MenuItem;
use App\Services\MenuService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MenuController extends Controller
{
    protected MenuService $menuService;

    public function __construct(MenuService $menuService)
    {
        $this->menuService = $menuService;
    }

    /**
     * Display list of all menu items
     */
    public function index(Request $request): View
    {
        $query = MenuItem::query();

        // Filter by category
        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        // Filter by availability
        if ($request->filled('availability')) {
            $query->where('is_available', $request->availability === 'available');
        }

        // Search
        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                    ->orWhere('description', 'like', '%' . $request->search . '%');
            });
        }

        $menuItems = $query->orderBy('category')->orderBy('name')->paginate(15);
        $categories = MenuItem::distinct()->pluck('category');

        return view('admin.menu.index', compact('menuItems', 'categories'));
    }

    /**
     * Show form for creating new menu item
     */
    public function create(): View
    {
        // pakai mapping key => label dari service (sama seperti kasir)
        $categories = $this->menuService->getCategories();

        return view('admin.menu.create', compact('categories'));
    }

    /**
     * Store a new menu item
     */
    public function store(MenuItemRequest $request): RedirectResponse
    {
        $data = $request->validated();

        // simpan file gambar jika ada
        if ($request->hasFile('image')) {
            $data['image_path'] = $request->file('image')->store('menu-items', 'public');
        }

        $this->menuService->createMenuItem($data);

        return redirect()
            ->route('admin.menu.index')
            ->with('success', 'Menu item berhasil ditambahkan!');
    }

    /**
     * Show form for editing menu item
     */
    public function edit(MenuItem $menu): View
    {
        // sama: pakai mapping service
        $categories = $this->menuService->getCategories();

        return view('admin.menu.edit', compact('menu', 'categories'));
    }

    /**
     * Update menu item
     */
    public function update(MenuItemRequest $request, MenuItem $menu): RedirectResponse
    {
        $data = $request->validated();

        if ($request->hasFile('image')) {
            $data['image_path'] = $request->file('image')->store('menu-items', 'public');
        }

        $this->menuService->updateMenuItem($menu->id, $data);

        return redirect()
            ->route('admin.menu.index')
            ->with('success', 'Menu item berhasil diperbarui!');
    }

    /**
     * Delete menu item
     */
    public function destroy(MenuItem $menu): RedirectResponse
    {
        $this->menuService->deleteMenuItem($menu->id);

        return redirect()
            ->route('admin.menu.index')
            ->with('success', 'Menu item berhasil dihapus!');
    }

    /**
     * Toggle availability status
     */
    public function toggleAvailability(MenuItem $menu): RedirectResponse
    {
        $menu->update(['is_available' => !$menu->is_available]);

        $status = $menu->is_available ? 'tersedia' : 'tidak tersedia';

        return redirect()
            ->back()
            ->with('success', "Menu '{$menu->name}' sekarang {$status}!");
    }
}
