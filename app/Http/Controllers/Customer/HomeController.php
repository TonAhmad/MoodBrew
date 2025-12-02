<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Services\AI\AiChatService;
use App\Services\AI\AiRecommendationService;
use App\Services\AuthService;
use App\Services\CustomerMenuService;
use Illuminate\View\View;

/**
 * HomeController - Handle customer home page dengan AI Chat
 */
class HomeController extends Controller
{
    public function __construct(
        protected AuthService $authService,
        protected AiChatService $chatService,
        protected AiRecommendationService $recommendationService,
        protected CustomerMenuService $menuService
    ) {}

    /**
     * Display customer home page dengan AI Chat interface
     */
    public function index(): View
    {
        $customer = $this->authService->getCurrentCustomer();
        
        // Check if AI is configured
        $aiAvailable = $this->isAiConfigured();
        
        // Get popular items for quick access
        $popularItems = $this->menuService->getPopularItems(4);
        
        // Get flash sales
        $flashSales = $this->menuService->getActiveFlashSales();

        return view('customer.home', [
            'customer' => $customer,
            'aiAvailable' => $aiAvailable,
            'popularItems' => $popularItems,
            'flashSales' => $flashSales,
        ]);
    }

    /**
     * Check if any AI service is configured
     */
    private function isAiConfigured(): bool
    {
        $apiKey = config('services.ai.api_key', '');
        return !empty($apiKey);
    }
}
