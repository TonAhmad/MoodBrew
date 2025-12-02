<?php

namespace App\Services\AI;

use App\Models\MenuItem;
use App\Models\MoodPrompt;
use Illuminate\Support\Collection;

/**
 * AI Chat Service
 * 
 * Service untuk percakapan interaktif dengan customer.
 * Bisa digunakan untuk chatbot yang lebih conversational.
 * 
 * @author [Nama Teman Anda]
 */
class AiChatService
{
    protected string $apiKey;
    protected string $apiUrl;
    protected string $model;

    /**
     * System prompt untuk AI chat
     */
    protected string $systemPrompt;

    public function __construct()
    {
        $this->apiKey = config('services.ai.api_key', '');
        $this->apiUrl = config('services.ai.api_url', 'https://api.openai.com/v1');
        $this->model = config('services.ai.model', 'gpt-3.5-turbo');

        $this->systemPrompt = 'Kamu adalah "Brew", asisten AI barista yang ramah dan empati di MoodBrew Cafe.' . "\n\n";
        $this->systemPrompt .= 'PERSONALITY:' . "\n";
        $this->systemPrompt .= '- Ramah, hangat, dan penuh empati' . "\n";
        $this->systemPrompt .= '- Suka menggunakan emoji yang relevan' . "\n";
        $this->systemPrompt .= '- Memahami bahasa Indonesia dengan baik' . "\n";
        $this->systemPrompt .= '- Bisa membantu customer memilih minuman berdasarkan mood' . "\n\n";
        $this->systemPrompt .= 'CAPABILITIES:' . "\n";
        $this->systemPrompt .= '1. Merekomendasikan minuman berdasarkan mood' . "\n";
        $this->systemPrompt .= '2. Menjelaskan detail menu (bahan, rasa, caffeine level)' . "\n";
        $this->systemPrompt .= '3. Memberikan info promo yang sedang berlangsung' . "\n";
        $this->systemPrompt .= '4. Menjawab pertanyaan umum tentang cafe' . "\n\n";
        $this->systemPrompt .= 'RULES:' . "\n";
        $this->systemPrompt .= '- Selalu gunakan bahasa Indonesia' . "\n";
        $this->systemPrompt .= '- Jawab dengan singkat tapi informatif (max 3 paragraf)' . "\n";
        $this->systemPrompt .= '- Jika tidak yakin, tanyakan untuk klarifikasi' . "\n";
        $this->systemPrompt .= '- Jangan memberikan informasi yang tidak ada di menu';
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
     * Kirim pesan chat dan dapatkan response AI
     * 
     * @param string $message Pesan dari customer
     * @param array $conversationHistory History percakapan sebelumnya
     * @param int|null $userId
     * @param string|null $sessionId
     * @return array Array dengan keys: success, message, suggested_menus, actions
     */
    public function chat(
        string $message,
        array $conversationHistory = [],
        ?int $userId = null,
        ?string $sessionId = null
    ): array {
        try {
            // 1. Build messages array dengan history
            $messages = $this->buildMessages($message, $conversationHistory);

            // 2. Call AI API
            $aiResponse = $this->callChatApi($messages);

            // 3. Parse response untuk extract menu suggestions
            $parsedResponse = $this->parseResponse($aiResponse);

            // 4. Save to database
            $this->saveChat($message, $aiResponse, $userId, $sessionId);

            return [
                'success' => true,
                'message' => $parsedResponse['message'],
                'suggested_menus' => $parsedResponse['menus'],
                'actions' => $parsedResponse['actions'],
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Maaf, saya sedang mengalami gangguan. Coba lagi nanti ya! 🙏',
                'suggested_menus' => [],
                'actions' => [],
            ];
        }
    }

    /**
     * Build messages array untuk API call
     * 
     * @param string $currentMessage
     * @param array $history
     * @return array
     */
    protected function buildMessages(string $currentMessage, array $history): array
    {
        $messages = [
            ['role' => 'system', 'content' => $this->systemPrompt],
        ];

        // Add menu context
        $menuContext = $this->getMenuContext();
        $messages[] = [
            'role' => 'system',
            'content' => "MENU TERSEDIA:\n{$menuContext}",
        ];

        // Add conversation history
        foreach ($history as $entry) {
            $messages[] = ['role' => 'user', 'content' => $entry['user'] ?? ''];
            $messages[] = ['role' => 'assistant', 'content' => $entry['assistant'] ?? ''];
        }

        // Add current message
        $messages[] = ['role' => 'user', 'content' => $currentMessage];

        return $messages;
    }

    /**
     * Get menu context untuk AI
     * 
     * @return string
     */
    protected function getMenuContext(): string
    {
        return MenuItem::query()
            ->where('is_available', true)
            ->get()
            ->map(fn(MenuItem $m) => $m->toAiContext())
            ->join("\n");
    }

    /**
     * Call Chat API
     * 
     * @param array $messages
     * @return string
     * 
     * TODO: Implement actual API call
     */
    protected function callChatApi(array $messages): string
    {
        // ============================================================
        // TODO: IMPLEMENT CHAT API CALL HERE
        // ============================================================
        // 
        // Contoh untuk OpenAI:
        // 
        // $response = Http::withHeaders([
        //     'Authorization' => 'Bearer ' . $this->apiKey,
        // ])->post($this->apiUrl . '/chat/completions', [
        //     'model' => $this->model,
        //     'messages' => $messages,
        //     'temperature' => 0.8,
        //     'max_tokens' => 300,
        // ]);
        // 
        // return $response->json('choices.0.message.content');
        // 
        // ============================================================

        // Mock response untuk development
        $lastMessage = end($messages)['content'] ?? '';

        if (str_contains(strtolower($lastMessage), 'menu')) {
            return "Tentu! 📋 Kami punya berbagai pilihan menu:\n\n" .
                "☕ **Coffee**: Espresso, Cappuccino, Latte\n" .
                "🍵 **Non-Coffee**: Matcha, Chocolate, Fresh Juice\n" .
                "🍰 **Food**: Croissant, Sandwich, Cake\n\n" .
                "Mau saya rekomendasikan sesuai mood kamu?";
        }

        return "Hai! 👋 Saya Brew, barista AI di MoodBrew.\n\n" .
            "Ceritakan mood kamu hari ini, dan saya akan carikan minuman yang pas untukmu! ☕✨";
    }

    /**
     * Parse AI response untuk extract menu suggestions
     * 
     * @param string $response
     * @return array
     */
    protected function parseResponse(string $response): array
    {
        // TODO: Implement proper parsing
        // Bisa menggunakan regex atau JSON parsing jika AI diminta response dalam format tertentu

        return [
            'message' => $response,
            'menus' => [], // Extract menu IDs jika ada
            'actions' => [], // Extract suggested actions (order, view_menu, etc)
        ];
    }

    /**
     * Save chat to database
     * 
     * @param string $userMessage
     * @param string $aiResponse
     * @param int|null $userId
     * @param string|null $sessionId
     * @return MoodPrompt
     */
    protected function saveChat(
        string $userMessage,
        string $aiResponse,
        ?int $userId,
        ?string $sessionId
    ): MoodPrompt {
        return MoodPrompt::create([
            'user_id' => $userId,
            'session_id' => $sessionId ?? session()->getId(),
            'user_input' => $userMessage,
            'ai_response' => $aiResponse,
        ]);
    }

    /**
     * Get quick reply suggestions berdasarkan context
     * 
     * @return array
     */
    public function getQuickReplies(): array
    {
        return [
            ['text' => '☕ Rekomendasi untukku', 'action' => 'recommend'],
            ['text' => '📋 Lihat menu', 'action' => 'menu'],
            ['text' => '🔥 Promo hari ini', 'action' => 'promo'],
            ['text' => '😊 Saya sedang senang', 'action' => 'mood_happy'],
            ['text' => '😔 Saya sedang lelah', 'action' => 'mood_tired'],
        ];
    }
}
