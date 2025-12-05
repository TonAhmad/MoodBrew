<?php

namespace App\Http\Controllers;

use App\Services\LandingService;
use Illuminate\View\View;

/**
 * LandingController - Handle landing page requests
 * 
 * Controller yang menggunakan LandingService untuk business logic
 * Mengikuti Single Responsibility Principle
 */
class LandingController extends Controller
{
    /**
     * @var LandingService
     */
    protected LandingService $landingService;

    /**
     * Constructor dengan dependency injection
     * 
     * @param LandingService $landingService
     */
    public function __construct(LandingService $landingService)
    {
        $this->landingService = $landingService;
    }

    /**
     * Display landing home page
     * 
     * @return View
     */
    public function home(): View
    {
        return view('landing.home', [
            'hero' => $this->landingService->getHeroData(),
            'features' => $this->landingService->getFeatures(),
            'steps' => $this->landingService->getHowItWorksSteps(),
            'flashSale' => $this->landingService->getActiveFlashSale(),
            'stats' => $this->landingService->getLandingStats(),
        ]);
    }

    /**
     * Display menu page (optional untuk landing)
     * 
     * @return View
     */
    public function menu(): View
    {
        return view('landing.menu', [
            'menuByCategory' => $this->landingService->getMenuByCategory(),
            'featuredItems' => $this->landingService->getFeaturedMenuItems(),
            'flashSale' => $this->landingService->getActiveFlashSale(),
        ]);
    }

    /**
     * Display public vibe wall
     * 
     * @return View
     */
    public function vibewall(): View
    {
        return view('landing.vibewall', [
            'vibes' => $this->landingService->getPublicVibes(),
            'stats' => $this->landingService->getVibeStats(),
        ]);
    }
}
