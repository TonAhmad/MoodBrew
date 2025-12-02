<?php

namespace App\Services;

use App\Models\CustomerInteraction;
use App\Models\Order;
use App\Services\AI\AiSentimentService;
use Illuminate\Support\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

/**
 * Empathy Radar Service
 * 
 * Handle business logic untuk real-time sentiment analysis feature.
 * AI sentiment analysis integration handled separately.
 */
class EmpathyRadarService
{
    protected AiSentimentService $aiSentimentService;

    public function __construct(AiSentimentService $aiSentimentService)
    {
        $this->aiSentimentService = $aiSentimentService;
    }

    /**
     * Get all customer interactions with pagination
     *
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function getAllInteractions(int $perPage = 15): LengthAwarePaginator
    {
        return CustomerInteraction::with(['order.customer', 'handledBy'])
            ->latest()
            ->paginate($perPage);
    }

    /**
     * Get today's interactions
     *
     * @return Collection
     */
    public function getTodayInteractions(): Collection
    {
        return CustomerInteraction::with(['order.customer', 'handledBy'])
            ->whereDate('created_at', today())
            ->latest()
            ->get();
    }

    /**
     * Get interactions that need attention (negative sentiment, unhandled)
     *
     * @return Collection
     */
    public function getInteractionsNeedingAttention(): Collection
    {
        return CustomerInteraction::with(['order.customer', 'handledBy'])
            ->whereIn('sentiment_type', ['negative', 'angry', 'frustrated'])
            ->whereNull('handled_by')
            ->latest()
            ->get();
    }

    /**
     * Create new interaction record
     *
     * @param array $data
     * @return CustomerInteraction
     */
    public function recordInteraction(array $data): CustomerInteraction
    {
        return CustomerInteraction::create([
            'order_id' => $data['order_id'] ?? null,
            'interaction_type' => $data['interaction_type'], // complaint, feedback, praise, question
            'channel' => $data['channel'] ?? 'in-store', // in-store, online, phone
            'customer_message' => $data['customer_message'],
            'sentiment_type' => $data['sentiment_type'] ?? 'neutral',
            'sentiment_score' => $data['sentiment_score'] ?? 0.5,
            'ai_analysis' => $data['ai_analysis'] ?? null,
            'suggested_response' => $data['suggested_response'] ?? null,
            'staff_notes' => $data['staff_notes'] ?? null,
            'handled_by' => auth()->id(),
            'resolved_at' => $data['is_resolved'] ?? false ? now() : null,
        ]);
    }

    /**
     * Mark interaction as handled
     *
     * @param int $interactionId
     * @param array $data
     * @return CustomerInteraction
     */
    public function handleInteraction(int $interactionId, array $data = []): CustomerInteraction
    {
        $interaction = CustomerInteraction::findOrFail($interactionId);

        $interaction->update([
            'handled_by' => auth()->id(),
            'staff_notes' => $data['staff_notes'] ?? $interaction->staff_notes,
            'resolved_at' => $data['is_resolved'] ?? false ? now() : null,
        ]);

        return $interaction->fresh();
    }

    /**
     * Get sentiment distribution for dashboard
     *
     * @param string $period (today, week, month)
     * @return array
     */
    public function getSentimentDistribution(string $period = 'today'): array
    {
        $query = CustomerInteraction::query();

        switch ($period) {
            case 'week':
                $query->whereBetween('created_at', [now()->startOfWeek(), now()]);
                break;
            case 'month':
                $query->whereMonth('created_at', now()->month);
                break;
            case 'today':
            default:
                $query->whereDate('created_at', today());
                break;
        }

        $total = $query->count();

        if ($total === 0) {
            return [
                'positive' => 0,
                'neutral' => 0,
                'negative' => 0,
                'total' => 0,
            ];
        }

        $positive = (clone $query)->whereIn('sentiment_type', ['positive', 'happy', 'satisfied'])->count();
        $negative = (clone $query)->whereIn('sentiment_type', ['negative', 'angry', 'frustrated', 'sad'])->count();
        $neutral = $total - $positive - $negative;

        return [
            'positive' => $positive,
            'negative' => $negative,
            'neutral' => $neutral,
            'total' => $total,
            'positive_percentage' => round(($positive / $total) * 100, 1),
            'negative_percentage' => round(($negative / $total) * 100, 1),
            'neutral_percentage' => round(($neutral / $total) * 100, 1),
        ];
    }

    /**
     * Get empathy radar statistics
     *
     * @return array
     */
    public function getRadarStats(): array
    {
        $today = today();

        $todayInteractions = CustomerInteraction::whereDate('created_at', $today);
        $needsAttention = CustomerInteraction::whereIn('sentiment_type', ['negative', 'angry', 'frustrated'])
            ->whereNull('handled_by')
            ->count();

        $avgSentimentScore = CustomerInteraction::whereDate('created_at', $today)
            ->avg('sentiment_score') ?? 0.5;

        return [
            'totalToday' => $todayInteractions->count(),
            'needsAttention' => $needsAttention,
            'averageSentiment' => round($avgSentimentScore, 2),
            'sentimentLabel' => $this->getSentimentLabel($avgSentimentScore),
        ];
    }

    /**
     * Get sentiment label from score
     *
     * @param float $score
     * @return string
     */
    protected function getSentimentLabel(float $score): string
    {
        if ($score >= 0.7) return 'Sangat Positif';
        if ($score >= 0.55) return 'Positif';
        if ($score >= 0.45) return 'Netral';
        if ($score >= 0.3) return 'Negatif';
        return 'Sangat Negatif';
    }

    /**
     * Check if AI service is available
     *
     * @return bool
     */
    public function isAiServiceAvailable(): bool
    {
        return $this->aiSentimentService->isConfigured();
    }
}
