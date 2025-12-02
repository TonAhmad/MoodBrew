<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Services\CustomerVibeService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * VibeWallController - Handle Vibe Wall untuk customer
 */
class VibeWallController extends Controller
{
    public function __construct(
        protected CustomerVibeService $vibeService
    ) {}

    /**
     * Display public vibe wall
     */
    public function index(): View
    {
        $vibes = $this->vibeService->getVibeWall(15);
        $featuredVibes = $this->vibeService->getFeaturedVibes(3);
        $moodEmojis = $this->vibeService->getMoodEmojis();

        return view('customer.vibewall.index', compact(
            'vibes',
            'featuredVibes',
            'moodEmojis'
        ));
    }

    /**
     * Show create vibe form
     */
    public function create(): View
    {
        $moodEmojis = $this->vibeService->getMoodEmojis();
        $myVibes = $this->vibeService->getMyVibes(auth()->id());

        return view('customer.vibewall.create', compact('moodEmojis', 'myVibes'));
    }

    /**
     * Store new vibe entry
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'message' => 'required|string|min:5|max:280',
            'customer_name' => 'nullable|string|max:50',
        ]);

        $result = $this->vibeService->postVibe([
            'message' => $request->message,
            'customer_name' => $request->customer_name ?? session('customer_name', 'Anonymous'),
        ]);

        if (!$result['success']) {
            return back()->with('error', $result['message']);
        }

        return redirect()->route('customer.vibewall.index')
            ->with('success', $result['message']);
    }
}
