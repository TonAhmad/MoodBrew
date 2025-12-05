<?php

namespace App\Services\AI;

use App\Models\MenuItem;
use App\Models\MoodPrompt;
use App\Services\KolosalAIService;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;

/**
 * AI Chat Service
 * 
 * Service untuk percakapan interaktif dengan customer.
 * Menggunakan KolosalAIService untuk AI integration.
 */
class AiChatService
{
    protected KolosalAIService $kolosalAI;

    /**
     * System prompt untuk AI chat
     */
    protected string $systemPrompt;

    public function __construct(KolosalAIService $kolosalAI)
    {
        $this->kolosalAI = $kolosalAI;

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
        return $this->kolosalAI->isEnabled();
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
            Log::error('AI Chat error: ' . $e->getMessage(), [
                'exception' => get_class($e),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);
            
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
     * Call Chat API menggunakan KolosalAIService
     * 
     * @param array $messages
     * @return string
     */
    protected function callChatApi(array $messages): string
    {
        // Convert messages format untuk KolosalAI
        // Gabungkan system + history + current message jadi satu prompt
        $systemMessages = array_values(array_filter($messages, fn($m) => $m['role'] === 'system'));
        $conversationMessages = array_values(array_filter($messages, fn($m) => $m['role'] !== 'system'));
        
        $systemPrompt = implode("\n", array_column($systemMessages, 'content'));
        
        // Build conversation context
        $conversationContext = [];
        foreach ($conversationMessages as $msg) {
            if ($msg['role'] === 'user') {
                $conversationContext[] = ['role' => 'user', 'content' => $msg['content']];
            } elseif ($msg['role'] === 'assistant') {
                $conversationContext[] = ['role' => 'assistant', 'content' => $msg['content']];
            }
        }
        
        // Get last user message
        $lastUserMessage = '';
        if (!empty($conversationMessages)) {
            for ($i = count($conversationMessages) - 1; $i >= 0; $i--) {
                if ($conversationMessages[$i]['role'] === 'user') {
                    $lastUserMessage = $conversationMessages[$i]['content'];
                    break;
                }
            }
        }
        
        // Build full prompt
        $fullPrompt = $systemPrompt . "\n\n" . $lastUserMessage;
        
        // Call KolosalAI
        $result = $this->kolosalAI->chat($fullPrompt, [
            'history' => count($conversationContext) > 1 ? array_slice($conversationContext, 0, -1) : []
        ]);
        
        if ($result && $result['success']) {
            return $result['content'];
        }
        
        Log::warning('KolosalAI chat failed, using fallback');
        
        // Fallback response
        return "Hai! 👋 Saya Brew, barista AI di MoodBrew.\n\nCeritakan mood kamu hari ini, dan saya akan carikan minuman yang pas untukmu! ☕✨";
    }

    /**
     * Parse AI response untuk extract menu suggestions
     * 
     * @param string $response
     * @return array
     */
    protected function parseResponse(string $response): array
    {
        $menus = [];
        
        // Extract menu items yang disebutkan AI
        // Cari nama menu dari database yang disebutkan dalam response
        $menuItems = MenuItem::select('id', 'name', 'price', 'description', 'category')
            ->where('is_available', true)
            ->get();
        
        foreach ($menuItems as $item) {
            // Check jika nama menu disebutkan di response (case insensitive)
            if (stripos($response, $item->name) !== false) {
                $menus[] = [
                    'id' => $item->id,
                    'name' => $item->name,
                    'price' => $item->price,
                    'description' => $item->description,
                    'category' => $item->category,
                ];
            }
        }
        
        // Limit to 4 suggestions
        $menus = array_slice($menus, 0, 4);

        return [
            'message' => $response,
            'menus' => $menus,
            'actions' => [],
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
