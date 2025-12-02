<?php

namespace App\Services\AI;

use App\Models\MenuItem;
use App\Models\MoodPrompt;
use Illuminate\Support\Collection;

/**
 * AI Recommendation Service
 * 
 * Service ini menangani rekomendasi menu berdasarkan mood customer.
 * Integrasi dengan AI API (OpenAI/Groq/Gemini) dilakukan di sini.
 * 
 * @author [Nama Teman Anda]
 * @see https://platform.openai.com/docs/api-reference (OpenAI)
 * @see https://console.groq.com/docs (Groq - Gratis)
 * @see https://ai.google.dev/docs (Gemini)
 */
class AiRecommendationService
{
    /**
     * API Key untuk AI service
     * Set di .env: AI_API_KEY=your_key_here
     */
    protected string $apiKey;

    /**
     * Base URL untuk AI API
     * Set di .env: AI_API_URL=https://api.openai.com/v1
     */
    protected string $apiUrl;

    /**
     * Model AI yang digunakan
     * Set di .env: AI_MODEL=gpt-3.5-turbo
     */
    protected string $model;

    public function __construct()
    {
        $this->apiKey = config('services.ai.api_key', '');
        $this->apiUrl = config('services.ai.api_url', 'https://api.openai.com/v1');
        $this->model = config('services.ai.model', 'gpt-3.5-turbo');
    }

    /**
     * Check if AI service is configured
     * 
     * @return bool True if API key is set
     */
    public function isConfigured(): bool
    {
        return !empty($this->apiKey) && $this->apiKey !== 'your_api_key_here';
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
     * Panggil AI API
     * 
     * @param string $prompt Prompt yang akan dikirim
     * @return string Response dari AI
     * 
     * TODO: Implement actual API call
     * Contoh implementasi untuk OpenAI:
     * 
     * $response = Http::withHeaders([
     *     'Authorization' => 'Bearer ' . $this->apiKey,
     *     'Content-Type' => 'application/json',
     * ])->post($this->apiUrl . '/chat/completions', [
     *     'model' => $this->model,
     *     'messages' => [
     *         ['role' => 'system', 'content' => 'You are a helpful barista AI'],
     *         ['role' => 'user', 'content' => $prompt]
     *     ],
     *     'temperature' => 0.7,
     *     'max_tokens' => 500,
     * ]);
     * 
     * return $response->json('choices.0.message.content');
     */
    protected function callAiApi(string $prompt): string
    {
        // ============================================================
        // TODO: IMPLEMENT AI API CALL HERE
        // ============================================================
        // 
        // Pilih salah satu provider:
        // 
        // 1. OpenAI (Berbayar)
        //    - API URL: https://api.openai.com/v1
        //    - Model: gpt-3.5-turbo, gpt-4
        //    - Docs: https://platform.openai.com/docs
        // 
        // 2. Groq (GRATIS & CEPAT)
        //    - API URL: https://api.groq.com/openai/v1
        //    - Model: llama-3.1-70b-versatile, mixtral-8x7b-32768
        //    - Docs: https://console.groq.com/docs
        // 
        // 3. Google Gemini (Gratis tier)
        //    - API URL: https://generativelanguage.googleapis.com/v1beta
        //    - Model: gemini-pro
        //    - Docs: https://ai.google.dev/docs
        // 
        // ============================================================

        // Temporary: Return mock response untuk development
        return json_encode([
            'mood_analysis' => 'Customer terdeteksi sedang lelah dan butuh energi',
            'recommendations' => [
                ['menu_id' => 1, 'reason' => 'Espresso shot untuk boost energi instan'],
                ['menu_id' => 2, 'reason' => 'Cappuccino dengan foam lembut yang comforting'],
                ['menu_id' => 3, 'reason' => 'Matcha Latte untuk energi yang lebih sustained'],
            ],
            'empathy_message' => 'Semangat! Semoga minuman pilihan kami bisa membuat harimu lebih baik ☕',
        ]);
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
                return collect();
            }

            $menuIds = collect($data['recommendations'])
                ->pluck('menu_id')
                ->filter()
                ->toArray();

            return MenuItem::whereIn('id', $menuIds)->get();
        } catch (\Exception $e) {
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
