<?php

namespace App\Http\Controllers\Cashier;

use App\Http\Controllers\Controller;
use App\Http\Requests\MenuItemRequest;
use App\Services\MenuService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * MenuController - Handle CRUD operasi menu untuk cashier
 * 
 * Cashier dapat menambah, mengedit, dan toggle availability menu
 */
class MenuController extends Controller
{
    /**
     * @var MenuService
     */
    protected MenuService $menuService;

    /**
     * Constructor dengan dependency injection
     */
    public function __construct(MenuService $menuService)
    {
        $this->menuService = $menuService;
    }

    /**
     * Display list of all menu items
     */
    public function index(Request $request): View
    {
        $search = $request->input('search');
        $category = $request->input('category');
        $availability = $request->has('available')
            ? filter_var($request->input('available'), FILTER_VALIDATE_BOOLEAN)
            : null;

        $menuItems = $this->menuService->getAllMenuItems(12, $search, $category, $availability);
        $stats = $this->menuService->getMenuStats();
        $categories = $this->menuService->getCategories();

        return view('cashier.menu.index', compact('menuItems', 'stats', 'categories', 'search', 'category'));
    }

    /**
     * Show form untuk create new menu item
     */
    public function create(): View
    {
        $categories = $this->menuService->getCategories();
        return view('cashier.menu.create', compact('categories'));
    }

    /**
     * Store new menu item ke database
     */
    public function store(MenuItemRequest $request): RedirectResponse
    {
        $result = $this->menuService->createMenuItem($request->validated());

        if (!$result['success']) {
            return back()
                ->withInput()
                ->withErrors(['name' => $result['message']]);
        }

        return redirect()
            ->route('cashier.menu.index')
            ->with('success', $result['message']);
    }

    /**
     * Show form untuk edit menu item
     */
    public function edit(int $menu): View
    {
        $menuItem = $this->menuService->getMenuItemById($menu);

        if (!$menuItem) {
            abort(404, 'Menu tidak ditemukan');
        }

        $categories = $this->menuService->getCategories();

        return view('cashier.menu.edit', compact('menuItem', 'categories'));
    }

    /**
     * Update menu item data
     */
    public function update(MenuItemRequest $request, int $menu): RedirectResponse
    {
        $result = $this->menuService->updateMenuItem($menu, $request->validated());

        if (!$result['success']) {
            return back()
                ->withInput()
                ->withErrors(['name' => $result['message']]);
        }

        return redirect()
            ->route('cashier.menu.index')
            ->with('success', $result['message']);
    }

    /**
     * Delete menu item
     */
    public function destroy(int $menu): RedirectResponse
    {
        $result = $this->menuService->deleteMenuItem($menu);

        if (!$result['success']) {
            return back()->with('error', $result['message']);
        }

        return redirect()
            ->route('cashier.menu.index')
            ->with('success', $result['message']);
    }

    /**
     * Toggle menu availability (AJAX friendly)
     */
    public function toggleAvailability(int $menu): RedirectResponse
    {
        $result = $this->menuService->toggleAvailability($menu);

        if (!$result['success']) {
            return back()->with('error', $result['message']);
        }

        return back()->with('success', $result['message']);
    }
}
