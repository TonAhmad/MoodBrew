<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Services\CustomerMenuService;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * MenuController - Handle menu browsing untuk customer
 */
class MenuController extends Controller
{
    public function __construct(
        protected CustomerMenuService $menuService
    ) {}

    /**
     * Display menu list with filters
     */
    public function index(Request $request): View
    {
        $filters = [
            'category' => $request->get('category'),
            'mood' => $request->get('mood'),
            'search' => $request->get('search'),
            'sort' => $request->get('sort', 'name'),
        ];

        $menuItems = $this->menuService->getMenuItems($filters);
        $categories = $this->menuService->getCategories();
        $flashSales = $this->menuService->getActiveFlashSales();
        $popularItems = $this->menuService->getPopularItems(4);

        return view('customer.menu.index', compact(
            'menuItems',
            'categories',
            'flashSales',
            'popularItems',
            'filters'
        ));
    }

    /**
     * Show single menu item detail
     */
    public function show(string $slug): View
    {
        $menuItem = $this->menuService->getMenuItem($slug);

        if (!$menuItem) {
            abort(404, 'Menu tidak ditemukan');
        }

        $flashSale = $this->menuService->getFlashSalePrice($menuItem);
        $relatedItems = $this->menuService->getMoodBasedItems(
            $menuItem->mood_tags[0] ?? 'happy',
            4
        );

        return view('customer.menu.show', compact(
            'menuItem',
            'flashSale',
            'relatedItems'
        ));
    }
}
