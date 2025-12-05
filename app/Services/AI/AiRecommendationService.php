<?php

namespace App\Services\AI;

use App\Models\MenuItem;
use App\Models\MoodPrompt;
use App\Services\KolosalAIService;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;

/**
 * AI Recommendation Service
 * 
 * Service ini menangani rekomendasi menu berdasarkan mood customer.
 * Menggunakan KolosalAIService untuk AI integration.
 */
class AiRecommendationService
{
    protected KolosalAIService $kolosalAI;

    public function __construct(KolosalAIService $kolosalAI)
    {
        $this->kolosalAI = $kolosalAI;
    }

    /**
     * Check if AI service is configured
     * 
     * @return bool True if API key is set
     */
    public function isConfigured(): bool
    {
        return $this->kolosalAI->isEnabled();
    }

    /**
     * Mendapatkan rekomendasi menu berdasarkan mood customer
     * 
     * @param string $userMood Input mood dari customer (contoh: "Saya lelah butuh energi")
     * @param int|null $userId User ID jika customer sudah login
     * @param string|null $sessionId Session ID untuk guest customer
     * @return array Array dengan keys: success, message, recommendations, prompt_id
     */
    public function getRecommendation(string $userMood, ?int $userId = null, ?string $sessionId = null): array
    {
        try {
            // 1. Ambil context menu yang tersedia
            $menuContext = $this->buildMenuContext();

            // 2. Build prompt untuk AI
            $prompt = $this->buildRecommendationPrompt($userMood, $menuContext);

            // 3. Panggil AI API
            $aiResponse = $this->callAiApi($prompt);

            // 4. Parse response AI menjadi menu recommendations
            $recommendations = $this->parseAiResponse($aiResponse);

            // 5. Simpan ke mood_prompts untuk history
            $moodPrompt = $this->saveMoodPrompt(
                userInput: $userMood,
                aiResponse: $aiResponse,
                recommendedMenuId: $recommendations->first()?->id,
                userId: $userId,
                sessionId: $sessionId
            );

            return [
                'success' => true,
                'message' => $aiResponse,
                'recommendations' => $recommendations->toArray(),
                'prompt_id' => $moodPrompt->id,
            ];
        } catch (\Exception $e) {
            // Fallback ke rekomendasi berbasis rule jika AI gagal
            return $this->getFallbackRecommendation($userMood);
        }
    }

    /**
     * Build context menu untuk dikirim ke AI
     * Menggunakan method toAiContext() dari MenuItem model
     * 
     * @return string Formatted menu context
     */
    protected function buildMenuContext(): string
    {
        $menuItems = MenuItem::query()
            ->where('is_available', true)
            ->where('stock_quantity', '>', 0)
            ->get();

        return $menuItems
            ->map(fn(MenuItem $item) => $item->toAiContext())
            ->join("\n");
    }

    /**
     * Build prompt untuk AI recommendation
     * 
     * @param string $userMood Mood input dari customer
     * @param string $menuContext Context menu yang tersedia
     * @return string Complete prompt
     * 
     * TODO: Customize prompt sesuai kebutuhan
     */
    protected function buildRecommendationPrompt(string $userMood, string $menuContext): string
    {
        $prompt = 'Kamu adalah barista AI yang ramah di MoodBrew Cafe. Tugasmu adalah merekomendasikan minuman berdasarkan mood customer.' . "\n\n";
        $prompt .= 'MOOD CUSTOMER:' . "\n";
        $prompt .= '"' . $userMood . '"' . "\n\n";
        $prompt .= 'MENU YANG TERSEDIA:' . "\n";
        $prompt .= $menuContext . "\n\n";
        $prompt .= 'INSTRUKSI:' . "\n";
        $prompt .= '1. Analisis mood customer dari input mereka' . "\n";
        $prompt .= '2. Pilih 3 menu yang paling cocok berdasarkan flavor profile' . "\n";
        $prompt .= '3. Berikan penjelasan singkat mengapa menu tersebut cocok' . "\n";
        $prompt .= '4. Gunakan bahasa Indonesia yang ramah dan empati' . "\n";
        $prompt .= '5. Format response dalam JSON seperti ini:' . "\n";
        $prompt .= '{' . "\n";
        $prompt .= '    "mood_analysis": "Analisis singkat mood customer",' . "\n";
        $prompt .= '    "recommendations": [' . "\n";
        $prompt .= '        {"menu_id": 1, "reason": "Alasan rekomendasi"},' . "\n";
        $prompt .= '        {"menu_id": 2, "reason": "Alasan rekomendasi"},' . "\n";
        $prompt .= '        {"menu_id": 3, "reason": "Alasan rekomendasi"}' . "\n";
        $prompt .= '    ],' . "\n";
        $prompt .= '    "empathy_message": "Pesan empati untuk customer"' . "\n";
        $prompt .= '}' . "\n\n";
        $prompt .= 'Response:';

        return $prompt;
    }

    /**
     * Panggil AI API menggunakan KolosalAIService
     * 
     * @param string $prompt Prompt yang akan dikirim
     * @return string Response dari AI
     */
    protected function callAiApi(string $prompt): string
    {
        $result = $this->kolosalAI->chat($prompt);
        
        if ($result && $result['success']) {
            // AI should return JSON, extract it
            $content = $result['content'];
            
            // Try to parse if it's already JSON
            $decoded = json_decode($content, true);
            if (json_last_error() === JSON_ERROR_NONE) {
                return $content;
            }
            
            // If not JSON, try to extract JSON from text
            if (preg_match('/\{.*\}/s', $content, $matches)) {
                return $matches[0];
            }
            
            Log::warning('AI response is not valid JSON', ['content' => $content]);
        }
        
        // Fallback: throw exception to trigger fallback recommendation
        throw new \Exception('AI API call failed');
    }

    /**
     * Parse response AI menjadi Collection of MenuItem
     * 
     * @param string $aiResponse JSON response dari AI
     * @return Collection<MenuItem>
     */
    protected function parseAiResponse(string $aiResponse): Collection
    {
        try {
            $data = json_decode($aiResponse, true);

            if (!isset($data['recommendations'])) {
                Log::warning('AI response missing recommendations field', ['response' => $aiResponse]);
                return collect();
            }

            // Extract menu names or IDs from recommendations
            $menuNames = collect($data['recommendations'])
                ->map(function($rec) {
                    // Handle both formats: ["menu_id" => 1] or ["menu" => "Espresso"]
                    if (isset($rec['menu_id'])) {
                        return $rec['menu_id'];
                    }
                    if (isset($rec['menu'])) {
                        return $rec['menu'];
                    }
                    return null;
                })
                ->filter()
                ->toArray();

            // Try to find by ID first
            $menuItems = MenuItem::whereIn('id', array_filter($menuNames, 'is_numeric'))->get();
            
            // If not found by ID, try by name
            if ($menuItems->isEmpty()) {
                $menuItems = MenuItem::query()
                    ->where(function($q) use ($menuNames) {
                        foreach ($menuNames as $name) {
                            if (!is_numeric($name)) {
                                $q->orWhere('name', 'LIKE', '%' . $name . '%');
                            }
                        }
                    })
                    ->where('is_available', true)
                    ->limit(3)
                    ->get();
            }

            return $menuItems;
        } catch (\Exception $e) {
            Log::error('Failed to parse AI response', [
                'error' => $e->getMessage(),
                'response' => $aiResponse
            ]);
            return collect();
        }
    }

    /**
     * Simpan prompt dan response ke database untuk history
     * 
     * @param string $userInput Input dari user
     * @param string $aiResponse Response dari AI
     * @param int|null $recommendedMenuId Menu yang direkomendasikan
     * @param int|null $userId User ID
     * @param string|null $sessionId Session ID
     * @return MoodPrompt
     */
    protected function saveMoodPrompt(
        string $userInput,
        string $aiResponse,
        ?int $recommendedMenuId,
        ?int $userId,
        ?string $sessionId
    ): MoodPrompt {
        return MoodPrompt::create([
            'user_id' => $userId,
            'session_id' => $sessionId ?? session()->getId(),
            'user_input' => $userInput,
            'ai_response' => $aiResponse,
            'recommended_menu_id' => $recommendedMenuId,
        ]);
    }

    /**
     * Fallback recommendation jika AI gagal
     * Menggunakan keyword matching sederhana
     * 
     * @param string $userMood
     * @return array
     */
    protected function getFallbackRecommendation(string $userMood): array
    {
        $mood = strtolower($userMood);

        // Simple keyword matching
        $moodKeywords = [
            'energi' => ['energizing', 'bold', 'strong'],
            'lelah' => ['energizing', 'refreshing'],
            'stress' => ['calming', 'smooth', 'comforting'],
            'santai' => ['smooth', 'mellow', 'calming'],
            'senang' => ['sweet', 'fruity', 'refreshing'],
            'sedih' => ['comforting', 'sweet', 'warm'],
        ];

        $matchedTags = [];
        foreach ($moodKeywords as $keyword => $tags) {
            if (str_contains($mood, $keyword)) {
                $matchedTags = array_merge($matchedTags, $tags);
            }
        }

        // Default tags jika tidak ada match
        if (empty($matchedTags)) {
            $matchedTags = ['balanced', 'smooth'];
        }

        // Query menu berdasarkan flavor notes
        $recommendations = MenuItem::query()
            ->where('is_available', true)
            ->where('stock_quantity', '>', 0)
            ->get()
            ->filter(function (MenuItem $item) use ($matchedTags) {
                $flavorNotes = $item->flavor_profile['flavor_notes'] ?? [];
                return count(array_intersect($matchedTags, $flavorNotes)) > 0;
            })
            ->take(3);

        // Jika masih kosong, ambil random
        if ($recommendations->isEmpty()) {
            $recommendations = MenuItem::query()
                ->where('is_available', true)
                ->where('stock_quantity', '>', 0)
                ->inRandomOrder()
                ->take(3)
                ->get();
        }

        return [
            'success' => true,
            'message' => 'Berikut rekomendasi kami berdasarkan mood Anda:',
            'recommendations' => $recommendations->toArray(),
            'prompt_id' => null,
            'is_fallback' => true,
        ];
    }

    /**
     * Get conversation history untuk customer
     * 
     * @param int|null $userId
     * @param string|null $sessionId
     * @param int $limit
     * @return Collection<MoodPrompt>
     */
    public function getConversationHistory(?int $userId, ?string $sessionId, int $limit = 10): Collection
    {
        return MoodPrompt::query()
            ->when($userId, fn($q) => $q->where('user_id', $userId))
            ->when(!$userId && $sessionId, fn($q) => $q->where('session_id', $sessionId))
            ->with('recommendedMenu')
            ->latest()
            ->limit($limit)
            ->get();
    }
}
