<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CustomerInteraction;
use App\Models\Order;
use App\Models\VibeEntry;
use App\Services\AI\AiSentimentService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * AnalyticsController - Handle Mood Analytics dashboard
 * Menampilkan analytics terkait mood dan sentiment customer
 */
class AnalyticsController extends Controller
{
    protected AiSentimentService $aiSentimentService;

    public function __construct(AiSentimentService $aiSentimentService)
    {
        $this->aiSentimentService = $aiSentimentService;
    }

    /**
     * Display mood analytics dashboard
     */
    public function index(Request $request): View
    {
        $period = $request->get('period', '7days');
        $aiAvailable = $this->aiSentimentService->isConfigured();

        // Get date range based on period
        $endDate = Carbon::now();
        $startDate = match ($period) {
            '7days' => Carbon::now()->subDays(7),
            '30days' => Carbon::now()->subDays(30),
            '90days' => Carbon::now()->subDays(90),
            default => Carbon::now()->subDays(7),
        };

        // Mood distribution from orders (customer_mood field)
        $moodDistribution = $this->getMoodDistribution($startDate, $endDate);

        // Sentiment trend from Vibe Wall
        $sentimentTrend = $this->getSentimentTrend($startDate, $endDate);

        // Customer interaction stats (Empathy Radar data)
        $interactionStats = $this->getInteractionStats($startDate, $endDate);

        // Peak mood times
        $peakMoodTimes = $this->getPeakMoodTimes($startDate, $endDate);

        // Summary stats
        $stats = [
            'totalVibeEntries' => VibeEntry::whereBetween('created_at', [$startDate, $endDate])->count(),
            'avgSentiment' => VibeEntry::whereBetween('created_at', [$startDate, $endDate])->avg('sentiment_score') ?? 0,
            'totalInteractions' => CustomerInteraction::whereBetween('created_at', [$startDate, $endDate])->count(),
            'positiveRatio' => $this->getPositiveRatio($startDate, $endDate),
        ];

        return view('admin.analytics.index', compact(
            'aiAvailable',
            'period',
            'moodDistribution',
            'sentimentTrend',
            'interactionStats',
            'peakMoodTimes',
            'stats'
        ));
    }

    /**
     * Get mood distribution from orders
     */
    private function getMoodDistribution(Carbon $startDate, Carbon $endDate): array
    {
        $moods = Order::whereBetween('created_at', [$startDate, $endDate])
            ->whereNotNull('customer_mood_summary')
            ->selectRaw('customer_mood_summary, COUNT(*) as count')
            ->groupBy('customer_mood_summary')
            ->pluck('count', 'customer_mood_summary')
            ->toArray();

        return $moods ?: [
            'happy' => 0,
            'relaxed' => 0,
            'energetic' => 0,
            'tired' => 0,
            'stressed' => 0,
        ];
    }

    /**
     * Get sentiment trend over time
     */
    private function getSentimentTrend(Carbon $startDate, Carbon $endDate): array
    {
        $trend = VibeEntry::whereBetween('created_at', [$startDate, $endDate])
            ->selectRaw('DATE(created_at) as date, AVG(sentiment_score) as avg_sentiment, COUNT(*) as count')
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->map(function ($item) {
                return [
                    'date' => $item->date,
                    'sentiment' => round($item->avg_sentiment ?? 0, 2),
                    'count' => $item->count,
                ];
            })
            ->toArray();

        return $trend;
    }

    /**
     * Get customer interaction statistics
     */
    private function getInteractionStats(Carbon $startDate, Carbon $endDate): array
    {
        $interactions = CustomerInteraction::whereBetween('created_at', [$startDate, $endDate]);

        return [
            'total' => $interactions->count(),
            'handled' => CustomerInteraction::whereBetween('created_at', [$startDate, $endDate])
                ->whereNotNull('resolved_at')->count(),
            'byType' => CustomerInteraction::whereBetween('created_at', [$startDate, $endDate])
                ->selectRaw('interaction_type, COUNT(*) as count')
                ->groupBy('interaction_type')
                ->pluck('count', 'interaction_type')
                ->toArray(),
            'avgResponseTime' => $this->getAvgResponseTime($startDate, $endDate),
        ];
    }

    /**
     * Get average response time in minutes
     */
    private function getAvgResponseTime(Carbon $startDate, Carbon $endDate): int
    {
        $avg = CustomerInteraction::whereBetween('created_at', [$startDate, $endDate])
            ->whereNotNull('resolved_at')
            ->selectRaw('AVG(TIMESTAMPDIFF(MINUTE, created_at, resolved_at)) as avg_time')
            ->value('avg_time');

        return (int) ($avg ?? 0);
    }

    /**
     * Get peak mood times (hour of day analysis)
     */
    private function getPeakMoodTimes(Carbon $startDate, Carbon $endDate): array
    {
        $peakTimes = Order::whereBetween('created_at', [$startDate, $endDate])
            ->whereNotNull('customer_mood_summary')
            ->selectRaw('HOUR(created_at) as hour, customer_mood_summary, COUNT(*) as count')
            ->groupBy('hour', 'customer_mood_summary')
            ->orderBy('hour')
            ->get()
            ->groupBy('hour')
            ->map(function ($group) {
                return $group->pluck('count', 'customer_mood_summary')->toArray();
            })
            ->toArray();

        return $peakTimes;
    }

    /**
     * Get positive sentiment ratio
     */
    private function getPositiveRatio(Carbon $startDate, Carbon $endDate): float
    {
        $total = VibeEntry::whereBetween('created_at', [$startDate, $endDate])->count();
        if ($total === 0) {
            return 0;
        }

        $positive = VibeEntry::whereBetween('created_at', [$startDate, $endDate])
            ->where('sentiment_score', '>=', 0.3)
            ->count();

        return round(($positive / $total) * 100, 1);
    }
}
