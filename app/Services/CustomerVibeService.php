<?php

namespace App\Services;

use App\Models\VibeEntry;
use App\Services\AI\AiSentimentService;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

/**
 * CustomerVibeService - Business logic untuk Vibe Wall customer side
 * 
 * Menangani:
 * - Post vibe/mood expression
 * - View vibe wall publik
 * - Like/react to vibes
 */
class CustomerVibeService
{
    public function __construct(
        protected AiSentimentService $sentimentService
    ) {}

    /**
     * Get approved vibe entries for public wall
     */
    public function getVibeWall(int $perPage = 10): LengthAwarePaginator
    {
        return VibeEntry::query()
            ->where('is_approved', true)
            ->with('user:id,name')
            ->orderBy('is_featured', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
    }

    /**
     * Get featured vibes
     */
    public function getFeaturedVibes(int $limit = 5): Collection
    {
        return VibeEntry::query()
            ->where('is_approved', true)
            ->where('is_featured', true)
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Post new vibe entry
     */
    public function postVibe(array $data): array
    {
        try {
            $sentimentScore = 0.5; // neutral default

            // If AI is configured, analyze sentiment
            if ($this->sentimentService->isConfigured()) {
                $analysis = $this->sentimentService->analyze($data['message']);
                if ($analysis['success']) {
                    $sentimentScore = $analysis['score'] ?? 0.5;
                }
            }

            $vibeEntry = VibeEntry::create([
                'user_id' => auth()->id(),
                'display_name' => $data['customer_name'] ?? session('customer_name', 'Anonymous'),
                'content' => $data['message'],
                'sentiment_score' => $sentimentScore,
                'is_approved' => false, // Need moderation
                'is_featured' => false,
            ]);

            return [
                'success' => true,
                'message' => 'Vibe berhasil diposting! Menunggu moderasi.',
                'vibe' => $vibeEntry,
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Gagal memposting vibe: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Get my vibes
     */
    public function getMyVibes(?int $userId = null): Collection
    {
        $query = VibeEntry::query();

        if ($userId) {
            $query->where('user_id', $userId);
        } else {
            $customerName = session('customer_name');
            if ($customerName) {
                $query->where('display_name', $customerName);
            } else {
                return collect([]);
            }
        }

        return $query->orderBy('created_at', 'desc')
            ->limit(20)
            ->get();
    }

    /**
     * Get mood emoji options
     */
    public function getMoodEmojis(): array
    {
        return [
            ['emoji' => '😊', 'label' => 'Happy', 'color' => 'yellow'],
            ['emoji' => '😌', 'label' => 'Relaxed', 'color' => 'green'],
            ['emoji' => '⚡', 'label' => 'Energetic', 'color' => 'orange'],
            ['emoji' => '😴', 'label' => 'Tired', 'color' => 'blue'],
            ['emoji' => '😤', 'label' => 'Stressed', 'color' => 'red'],
            ['emoji' => '🥰', 'label' => 'Loved', 'color' => 'pink'],
            ['emoji' => '🤔', 'label' => 'Thoughtful', 'color' => 'purple'],
            ['emoji' => '☕', 'label' => 'Coffee Time', 'color' => 'brown'],
        ];
    }
}
