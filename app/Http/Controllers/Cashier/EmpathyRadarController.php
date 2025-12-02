<?php

namespace App\Http\Controllers\Cashier;

use App\Http\Controllers\Controller;
use App\Services\EmpathyRadarService;
use App\Services\AI\AiSentimentService;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

/**
 * Empathy Radar Controller for Cashier
 * 
 * Handle real-time sentiment analysis feature.
 * AI sentiment integration untuk analyze customer interactions.
 */
class EmpathyRadarController extends Controller
{
    protected EmpathyRadarService $empathyRadarService;
    protected AiSentimentService $aiSentimentService;

    public function __construct(
        EmpathyRadarService $empathyRadarService,
        AiSentimentService $aiSentimentService
    ) {
        $this->empathyRadarService = $empathyRadarService;
        $this->aiSentimentService = $aiSentimentService;
    }

    /**
     * Display empathy radar dashboard
     *
     * @return View
     */
    public function index(): View
    {
        $interactions = $this->empathyRadarService->getTodayInteractions();
        $needsAttention = $this->empathyRadarService->getInteractionsNeedingAttention();
        $stats = $this->empathyRadarService->getRadarStats();
        $distribution = $this->empathyRadarService->getSentimentDistribution('today');
        $isAiAvailable = $this->empathyRadarService->isAiServiceAvailable();

        return view('cashier.empathy.index', [
            'interactions' => $interactions,
            'needsAttention' => $needsAttention,
            'stats' => $stats,
            'distribution' => $distribution,
            'isAiAvailable' => $isAiAvailable,
        ]);
    }

    /**
     * Show form to record new interaction
     *
     * @return View
     */
    public function create(): View
    {
        $isAiAvailable = $this->empathyRadarService->isAiServiceAvailable();

        return view('cashier.empathy.create', [
            'isAiAvailable' => $isAiAvailable,
        ]);
    }

    /**
     * Store new interaction record
     *
     * @param Request $request
     * @return RedirectResponse
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'interaction_type' => 'required|in:complaint,feedback,praise,question',
            'channel' => 'required|in:in-store,online,phone',
            'customer_message' => 'required|string|max:1000',
            'staff_notes' => 'nullable|string|max:500',
            'is_resolved' => 'nullable|boolean',
        ]);

        try {
            // Try to analyze sentiment with AI if available
            if ($this->aiSentimentService->isConfigured()) {
                try {
                    $analysis = $this->aiSentimentService->analyzeSentiment($validated['customer_message']);
                    $validated['sentiment_type'] = $analysis['sentiment'] ?? 'neutral';
                    $validated['sentiment_score'] = $analysis['score'] ?? 0.5;
                    $validated['ai_analysis'] = $analysis['analysis'] ?? null;
                    $validated['suggested_response'] = $analysis['suggested_response'] ?? null;
                } catch (\Exception $e) {
                    // AI failed, use default values
                    $validated['sentiment_type'] = 'neutral';
                    $validated['sentiment_score'] = 0.5;
                }
            } else {
                // AI not available, use manual classification based on type
                $sentimentMap = [
                    'complaint' => ['type' => 'negative', 'score' => 0.3],
                    'praise' => ['type' => 'positive', 'score' => 0.8],
                    'feedback' => ['type' => 'neutral', 'score' => 0.5],
                    'question' => ['type' => 'neutral', 'score' => 0.5],
                ];

                $mapping = $sentimentMap[$validated['interaction_type']] ?? ['type' => 'neutral', 'score' => 0.5];
                $validated['sentiment_type'] = $mapping['type'];
                $validated['sentiment_score'] = $mapping['score'];
            }

            $interaction = $this->empathyRadarService->recordInteraction($validated);

            return redirect()
                ->route('cashier.empathy.index')
                ->with('success', 'Interaksi pelanggan berhasil dicatat.');
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Gagal mencatat interaksi: ' . $e->getMessage());
        }
    }

    /**
     * Mark interaction as handled
     *
     * @param Request $request
     * @param int $id
     * @return RedirectResponse
     */
    public function handle(Request $request, int $id): RedirectResponse
    {
        $validated = $request->validate([
            'staff_notes' => 'nullable|string|max:500',
            'is_resolved' => 'nullable|boolean',
        ]);

        try {
            $this->empathyRadarService->handleInteraction($id, $validated);

            return redirect()
                ->route('cashier.empathy.index')
                ->with('success', 'Interaksi berhasil ditangani.');
        } catch (\Exception $e) {
            return back()
                ->with('error', 'Gagal menangani interaksi: ' . $e->getMessage());
        }
    }
}
