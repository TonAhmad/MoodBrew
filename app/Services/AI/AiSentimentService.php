<?php

namespace App\Services\AI;

use App\Models\VibeEntry;
use Illuminate\Support\Collection;

/**
 * AI Sentiment Analysis Service
 * 
 * Service untuk analisis sentiment dari:
 * - Vibe Wall entries
 * - Customer feedback
 * - Order notes
 * 
 * Digunakan untuk:
 * - Empathy Radar (Cashier Dashboard)
 * - Vibe Wall moderation (Admin)
 * - Customer mood tracking
 * 
 * @author [Nama Teman Anda]
 */
class AiSentimentService
{
    protected string $apiKey;
    protected string $apiUrl;
    protected string $model;

    public function __construct()
    {
        $this->apiKey = config('services.ai.api_key', '');
        $this->apiUrl = config('services.ai.api_url', 'https://api.openai.com/v1');
        $this->model = config('services.ai.model', 'gpt-3.5-turbo');
    }

    /**
     * Check if AI service is configured and ready
     * 
     * @return bool
     */
    public function isConfigured(): bool
    {
        return !empty($this->apiKey);
    }

    /**
     * Analyze sentiment dari text
     * 
     * @param string $text Text yang akan dianalisis
     * @return array Array dengan keys: score, label, emotions, summary
     */
    public function analyzeSentiment(string $text): array
    {
        $prompt = $this->buildSentimentPrompt($text);
        $response = $this->callAiApi($prompt);

        return $this->parseSentimentResponse($response);
    }

    /**
     * Analyze sentiment untuk Vibe Entry dan update di database
     * 
     * @param VibeEntry $vibeEntry
     * @return VibeEntry Updated entry
     */
    public function analyzeVibeEntry(VibeEntry $vibeEntry): VibeEntry
    {
        $result = $this->analyzeSentiment($vibeEntry->content);

        $vibeEntry->update([
            'sentiment_score' => $result['score'],
        ]);

        return $vibeEntry->fresh();
    }

    /**
     * Batch analyze multiple texts
     * 
     * @param array $texts Array of strings to analyze
     * @return array Array of sentiment results
     */
    public function batchAnalyze(array $texts): array
    {
        // Untuk efisiensi, bisa digabung dalam 1 API call
        // atau process secara paralel

        return collect($texts)
            ->map(fn($text) => $this->analyzeSentiment($text))
            ->toArray();
    }

    /**
     * Get mood summary untuk customer (Empathy Radar)
     * Menganalisis beberapa input terakhir dari customer
     * 
     * @param int|null $userId
     * @param string|null $sessionId
     * @return array{
     *     overall_mood: string,
     *     mood_score: float,
     *     recommendation: string,
     *     emoji: string
     * }
     */
    public function getCustomerMoodSummary(?int $userId, ?string $sessionId): array
    {
        // Ambil recent interactions
        $recentInputs = \App\Models\MoodPrompt::query()
            ->when($userId, fn($q) => $q->where('user_id', $userId))
            ->when(!$userId && $sessionId, fn($q) => $q->where('session_id', $sessionId))
            ->latest()
            ->take(5)
            ->pluck('user_input')
            ->toArray();

        if (empty($recentInputs)) {
            return [
                'overall_mood' => 'neutral',
                'mood_score' => 0.0,
                'recommendation' => 'Belum ada interaksi',
                'emoji' => '😊',
            ];
        }

        // Analyze combined inputs
        $combinedText = implode('. ', $recentInputs);
        $sentiment = $this->analyzeSentiment($combinedText);

        return [
            'overall_mood' => $sentiment['label'],
            'mood_score' => $sentiment['score'],
            'recommendation' => $this->getMoodRecommendation($sentiment['label']),
            'emoji' => $this->getMoodEmoji($sentiment['score']),
        ];
    }

    /**
     * Detect inappropriate content untuk moderation
     * 
     * @param string $text
     * @return array Array dengan keys: is_appropriate, reason, confidence
     */
    public function moderateContent(string $text): array
    {
        $prompt = 'Analisis apakah text berikut aman untuk ditampilkan di public wall sebuah cafe.' . "\n\n";
        $prompt .= 'Text: "' . $text . '"' . "\n\n";
        $prompt .= 'Check untuk:' . "\n";
        $prompt .= '1. Kata-kata kasar/tidak sopan' . "\n";
        $prompt .= '2. Konten berbahaya/kekerasan' . "\n";
        $prompt .= '3. Spam/promosi' . "\n";
        $prompt .= '4. Informasi pribadi' . "\n\n";
        $prompt .= 'Response dalam JSON:' . "\n";
        $prompt .= '{' . "\n";
        $prompt .= '    "is_appropriate": true/false,' . "\n";
        $prompt .= '    "reason": "alasan jika tidak appropriate atau null",' . "\n";
        $prompt .= '    "confidence": 0.0-1.0' . "\n";
        $prompt .= '}';

        $response = $this->callAiApi($prompt);

        try {
            return json_decode($response, true) ?? [
                'is_appropriate' => true,
                'reason' => null,
                'confidence' => 0.5,
            ];
        } catch (\Exception $e) {
            return [
                'is_appropriate' => true,
                'reason' => null,
                'confidence' => 0.5,
            ];
        }
    }

    /**
     * Build sentiment analysis prompt
     */
    protected function buildSentimentPrompt(string $text): string
    {
        $prompt = 'Analisis sentiment dari text berikut. Text ini dari customer cafe.' . "\n\n";
        $prompt .= 'Text: "' . $text . '"' . "\n\n";
        $prompt .= 'Response dalam JSON:' . "\n";
        $prompt .= '{' . "\n";
        $prompt .= '    "score": -1.0 sampai 1.0 (negatif ke positif),' . "\n";
        $prompt .= '    "label": "negative" / "neutral" / "positive",' . "\n";
        $prompt .= '    "emotions": ["array", "of", "detected", "emotions"],' . "\n";
        $prompt .= '    "summary": "ringkasan singkat mood customer dalam bahasa Indonesia"' . "\n";
        $prompt .= '}' . "\n\n";
        $prompt .= 'Response:';

        return $prompt;
    }

    /**
     * Call AI API
     * 
     * TODO: Implement actual API call
     */
    protected function callAiApi(string $prompt): string
    {
        // ============================================================
        // TODO: IMPLEMENT AI API CALL HERE
        // ============================================================

        // Mock response untuk development
        $text = strtolower($prompt);

        // Simple keyword-based mock
        if (str_contains($text, 'senang') || str_contains($text, 'enak') || str_contains($text, 'bagus')) {
            return json_encode([
                'score' => 0.8,
                'label' => 'positive',
                'emotions' => ['happy', 'satisfied'],
                'summary' => 'Customer dalam mood positif dan puas',
            ]);
        }

        if (str_contains($text, 'sedih') || str_contains($text, 'lelah') || str_contains($text, 'stress')) {
            return json_encode([
                'score' => -0.3,
                'label' => 'negative',
                'emotions' => ['tired', 'stressed'],
                'summary' => 'Customer terlihat lelah atau stress',
            ]);
        }

        return json_encode([
            'score' => 0.1,
            'label' => 'neutral',
            'emotions' => ['calm'],
            'summary' => 'Customer dalam keadaan normal',
        ]);
    }

    /**
     * Parse sentiment response
     */
    protected function parseSentimentResponse(string $response): array
    {
        try {
            $data = json_decode($response, true);

            return [
                'score' => (float) ($data['score'] ?? 0),
                'label' => $data['label'] ?? 'neutral',
                'emotions' => $data['emotions'] ?? [],
                'summary' => $data['summary'] ?? '',
            ];
        } catch (\Exception $e) {
            return [
                'score' => 0,
                'label' => 'neutral',
                'emotions' => [],
                'summary' => '',
            ];
        }
    }

    /**
     * Get mood-based service recommendation for cashier
     */
    protected function getMoodRecommendation(string $mood): string
    {
        return match ($mood) {
            'positive' => 'Customer dalam mood baik! Tawarkan upsell atau promo.',
            'negative' => 'Customer mungkin butuh perhatian extra. Berikan service terbaik.',
            default => 'Layani dengan ramah seperti biasa.',
        };
    }

    /**
     * Get emoji for mood score
     */
    protected function getMoodEmoji(float $score): string
    {
        return match (true) {
            $score > 0.5 => '😄',
            $score > 0.2 => '😊',
            $score > -0.2 => '😐',
            $score > -0.5 => '😔',
            default => '😢',
        };
    }
}
