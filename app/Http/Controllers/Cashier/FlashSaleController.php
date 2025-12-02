<?php

namespace App\Http\Controllers\Cashier;

use App\Http\Controllers\Controller;
use App\Services\FlashSaleService;
use App\Services\AI\AiCopywritingService;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

/**
 * Flash Sale Controller for Cashier
 * 
 * Handle Dead-hour Flash Sales feature.
 * AI copywriting integration untuk generate promo copy.
 */
class FlashSaleController extends Controller
{
    protected FlashSaleService $flashSaleService;
    protected AiCopywritingService $aiCopywritingService;

    public function __construct(
        FlashSaleService $flashSaleService,
        AiCopywritingService $aiCopywritingService
    ) {
        $this->flashSaleService = $flashSaleService;
        $this->aiCopywritingService = $aiCopywritingService;
    }

    /**
     * Display flash sale management page
     *
     * @return View
     */
    public function index(): View
    {
        $flashSales = $this->flashSaleService->getAllFlashSales();
        $activeFlashSale = $this->flashSaleService->getActiveFlashSale();
        $stats = $this->flashSaleService->getFlashSaleStats();
        $suggestions = $this->flashSaleService->getFlashSaleSuggestions();
        $isAiAvailable = $this->aiCopywritingService->isConfigured();

        return view('cashier.flashsale.index', [
            'flashSales' => $flashSales,
            'activeFlashSale' => $activeFlashSale,
            'stats' => $stats,
            'suggestions' => $suggestions,
            'isAiAvailable' => $isAiAvailable,
        ]);
    }

    /**
     * Show create flash sale form
     *
     * @return View
     */
    public function create(): View
    {
        $suggestions = $this->flashSaleService->getFlashSaleSuggestions();
        $isAiAvailable = $this->aiCopywritingService->isConfigured();

        return view('cashier.flashsale.create', [
            'suggestions' => $suggestions,
            'isAiAvailable' => $isAiAvailable,
        ]);
    }

    /**
     * Store new flash sale
     *
     * @param Request $request
     * @return RedirectResponse
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'discount_percentage' => 'required|integer|min:5|max:50',
            'trigger_reason' => 'nullable|string|max:255',
            'duration_hours' => 'required|integer|min:1|max:24',
        ]);

        try {
            // Calculate end time
            $validated['starts_at'] = now();
            $validated['ends_at'] = now()->addHours($validated['duration_hours']);

            // Try to generate AI copy if available
            $aiCopy = null;
            if ($this->aiCopywritingService->isConfigured()) {
                try {
                    $aiCopy = $this->aiCopywritingService->generateFlashSaleCopy(
                        $validated['name'],
                        $validated['discount_percentage'],
                        $validated['duration_hours'] * 60
                    );
                } catch (\Exception $e) {
                    // AI failed, continue without copy
                }
            }

            $validated['ai_generated_copy'] = $aiCopy;

            $flashSale = $this->flashSaleService->createFlashSale($validated);

            return redirect()
                ->route('cashier.flashsale.index')
                ->with('success', "Flash Sale '{$flashSale->name}' berhasil diaktifkan! Kode: {$flashSale->promo_code}");
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Gagal membuat flash sale: ' . $e->getMessage());
        }
    }

    /**
     * End active flash sale
     *
     * @param int $id
     * @return RedirectResponse
     */
    public function end(int $id): RedirectResponse
    {
        try {
            $flashSale = $this->flashSaleService->endFlashSale($id);

            return redirect()
                ->route('cashier.flashsale.index')
                ->with('success', "Flash Sale '{$flashSale->name}' berhasil diakhiri.");
        } catch (\Exception $e) {
            return back()
                ->with('error', 'Gagal mengakhiri flash sale: ' . $e->getMessage());
        }
    }
}
