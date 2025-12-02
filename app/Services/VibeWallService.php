<?php

namespace App\Services;

use App\Models\VibeEntry;
use App\Services\AI\AiSentimentService;
use Illuminate\Support\Collection;

/**
 * VibeWallService - Business logic untuk Vibe Wall
 */
class VibeWallService
{
    protected AiSentimentService $aiSentimentService;

    public function __construct(AiSentimentService $aiSentimentService)
    {
        $this->aiSentimentService = $aiSentimentService;
    }

    /**
     * Create new vibe entry
     */
    public function createEntry(array $data): VibeEntry
    {
        $entry = VibeEntry::create([
            'content' => $data['content'],
            'display_name' => $data['display_name'] ?? 'Anonymous',
            'user_id' => $data['user_id'] ?? null,
            'is_approved' => false, // Requires moderation
            'is_featured' => false,
            'sentiment_score' => 0, // Will be analyzed by AI
        ]);

        // Auto-analyze sentiment if AI is configured
        if ($this->aiSentimentService->isConfigured()) {
            $this->analyzeEntrySentiment($entry);
        }

        return $entry;
    }

    /**
     * Analyze sentiment for an entry
     */
    public function analyzeEntrySentiment(VibeEntry $entry): VibeEntry
    {
        if (!$this->aiSentimentService->isConfigured()) {
            return $entry;
        }

        $result = $this->aiSentimentService->analyzeSentiment($entry->content);
        $entry->update([
            'sentiment_score' => $result['score'] ?? 0,
        ]);

        return $entry->fresh();
    }

    /**
     * Get approved entries for public display
     */
    public function getApprovedEntries(int $limit = 20): Collection
    {
        return VibeEntry::approved()
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Get featured entries
     */
    public function getFeaturedEntries(int $limit = 5): Collection
    {
        return VibeEntry::where('is_featured', true)
            ->where('is_approved', true)
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Get pending entries for moderation
     */
    public function getPendingEntries(): Collection
    {
        return VibeEntry::where('is_approved', false)
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Approve entry
     */
    public function approveEntry(VibeEntry $entry): VibeEntry
    {
        $entry->update(['is_approved' => true]);
        return $entry->fresh();
    }

    /**
     * Reject and delete entry
     */
    public function rejectEntry(VibeEntry $entry): void
    {
        $entry->delete();
    }

    /**
     * Toggle featured status
     */
    public function toggleFeatured(VibeEntry $entry): VibeEntry
    {
        $entry->update(['is_featured' => !$entry->is_featured]);
        return $entry->fresh();
    }

    /**
     * Get sentiment statistics
     */
    public function getSentimentStats(): array
    {
        $entries = VibeEntry::approved()->get();

        if ($entries->isEmpty()) {
            return [
                'total' => 0,
                'positive' => 0,
                'neutral' => 0,
                'negative' => 0,
                'avg_score' => 0,
            ];
        }

        return [
            'total' => $entries->count(),
            'positive' => $entries->where('sentiment_score', '>=', 0.3)->count(),
            'neutral' => $entries->whereBetween('sentiment_score', [-0.3, 0.3])->count(),
            'negative' => $entries->where('sentiment_score', '<=', -0.3)->count(),
            'avg_score' => round($entries->avg('sentiment_score'), 2),
        ];
    }

    /**
     * Auto-moderate entry based on sentiment
     * Negative entries might need manual review
     */
    public function autoModerate(VibeEntry $entry): bool
    {
        // If AI is not configured, always require manual review
        if (!$this->aiSentimentService->isConfigured()) {
            return false;
        }

        // Auto-approve if sentiment is positive or neutral
        if ($entry->sentiment_score >= -0.2) {
            $entry->update(['is_approved' => true]);
            return true;
        }

        // Negative entries need manual review
        return false;
    }
}
