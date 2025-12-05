<?php

namespace App\Services\AI;

use App\Models\VibeEntry;
use App\Services\KolosalAIService;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;

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
    protected KolosalAIService $kolosalAI;

    public function __construct(KolosalAIService $kolosalAIService)
    {
        $this->kolosalAI = $kolosalAIService;
    }

    /**
     * Check if AI service is configured and ready
     * 
     * @return bool
     */
    public function isConfigured(): bool
    {
        // Check if AI is enabled and API key is configured
        return config('services.ai.enabled', false) && !empty(config('services.ai.api_key'));
    }

    /**
     * Analyze sentiment dari text
     * 
     * @param string $text Text yang akan dianalisis
     * @return array Array dengan keys: sentiment, score, analysis, suggested_response, emotions, summary
     */
    public function analyzeSentiment(string $text): array
    {
        $prompt = $this->buildSentimentPrompt($text);
        $response = $this->callAiApi($prompt);

        $parsed = $this->parseSentimentResponse($response);
        
        // Return format yang sesuai dengan Empathy Radar controller
        return [
            'sentiment' => $parsed['label'] ?? 'neutral', // Map label -> sentiment
            'score' => $parsed['score'] ?? 0.5,
            'analysis' => $parsed['summary'] ?? '',
            'suggested_response' => $this->generateSuggestedResponse($parsed['label'] ?? 'neutral', $text),
            'emotions' => $parsed['emotions'] ?? [],
            'summary' => $parsed['summary'] ?? '',
        ];
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
     */
    protected function callAiApi(string $prompt): string
    {
        try {
            $result = $this->kolosalAI->chat($prompt);
            
            if ($result && $result['success']) {
                return $result['content'];
            }
            
            Log::warning('KolosalAI sentiment analysis failed, using fallback');
            return $this->getFallbackResponse($prompt);
        } catch (\Exception $e) {
            Log::error('AI Sentiment error: ' . $e->getMessage());
            return $this->getFallbackResponse($prompt);
        }
    }

    /**
     * Get fallback response when AI fails
     */
    protected function getFallbackResponse(string $prompt): string
    {
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
     * Get mood emoji for mood score
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

    /**
     * Generate suggested response for staff based on sentiment
     */
    protected function generateSuggestedResponse(string $sentiment, string $customerMessage): string
    {
        return match ($sentiment) {
            'positive' => 'Terima kasih atas feedback positifnya! Kami senang Anda menikmati pengalaman di MoodBrew.',
            'negative' => 'Mohon maaf atas ketidaknyamanannya. Kami akan segera menindaklanjuti masalah ini.',
            default => 'Terima kasih atas feedback Anda. Kami menghargai masukan Anda untuk meningkatkan layanan.',
        };
    }
}
