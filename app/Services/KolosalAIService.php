<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Exception;

class KolosalAIService
{
    protected string $apiKey;
    protected string $apiUrl;
    protected string $model;
    protected float $temperature;
    protected int $maxTokens;
    protected bool $enabled;
    protected bool $fallbackEnabled;

    public function __construct()
    {
        $this->apiKey = config('services.ai.api_key');
        $this->apiUrl = config('services.ai.api_url');
        $this->model = config('services.ai.model');
        $this->temperature = config('services.ai.temperature', 0.7);
        $this->maxTokens = config('services.ai.max_tokens', 500);
        $this->enabled = config('services.ai.enabled', true);
        $this->fallbackEnabled = config('services.ai.fallback_enabled', true);
    }

    /**
     * Panggil Kolosal AI dengan prompt tertentu
     * 
     * @param string $prompt Prompt/pertanyaan ke AI
     * @param array $context Data konteks tambahan (opsional)
     * @return array|null Response dari AI atau null jika gagal
     */
    public function chat(string $prompt, array $context = []): ?array
    {
        if (!$this->enabled) {
            Log::info('AI Service disabled, skipping AI call');
            return null;
        }

        if (empty($this->apiKey)) {
            Log::error('AI API Key tidak ditemukan di .env');
            return null;
        }

        try {
            // Format request sesuai dokumentasi Kolosal AI
            // Struktur: model, messages, max_tokens (opsional: temperature)
            $messages = [
                [
                    'role' => 'system',
                    'content' => 'Kamu adalah AI assistant untuk MoodBrew, sebuah aplikasi cafe yang merekomendasikan minuman berdasarkan mood customer.'
                ],
                [
                    'role' => 'user',
                    'content' => $prompt
                ]
            ];

            // Tambahkan conversation history jika ada di context
            if (!empty($context['history'])) {
                array_unshift($messages, ...$context['history']);
            }

            $payload = [
                'model' => $this->model,
                'messages' => $messages,
                'max_tokens' => $this->maxTokens,
            ];

            // Temperature opsional, tambahkan jika diset
            if ($this->temperature > 0) {
                $payload['temperature'] = $this->temperature;
            }

            Log::info('Sending request to Kolosal AI', [
                'prompt_length' => strlen($prompt),
                'has_context' => !empty($context)
            ]);

            // Kirim request ke Kolosal AI
            $response = Http::timeout(30)
                ->withHeaders([
                    'Content-Type' => 'application/json',
                    'Authorization' => 'Bearer ' . $this->apiKey, // Gunakan Bearer token
                ])
                ->post($this->apiUrl . '/chat/completions', $payload);

            if ($response->successful()) {
                $data = $response->json();
                
                Log::info('Kolosal AI response received', [
                    'status' => $response->status(),
                    'model' => $data['model'] ?? 'unknown',
                    'usage' => $data['usage'] ?? []
                ]);

                return [
                    'success' => true,
                    'content' => $data['choices'][0]['message']['content'] ?? '',
                    'finish_reason' => $data['choices'][0]['finish_reason'] ?? '',
                    'usage' => $data['usage'] ?? [],
                    'raw_response' => $data,
                ];
            }

            // Handle error responses (400, 401, 500)
            $errorData = $response->json();
            $errorMessage = $errorData['error']['message'] ?? $errorData['message'] ?? 'Unknown error';
            
            Log::error('Kolosal AI request failed', [
                'status' => $response->status(),
                'error' => $errorMessage,
                'body' => $response->body()
            ]);

            return null;

        } catch (Exception $e) {
            Log::error('Kolosal AI exception: ' . $e->getMessage(), [
                'exception' => get_class($e),
                'trace' => $e->getTraceAsString()
            ]);

            return null;
        }
    }

    /**
     * Analisa mood customer dari input text
     * 
     * @param string $customerInput Input dari customer
     * @return array|null Mood analysis
     */
    public function analyzeMood(string $customerInput): ?array
    {
        $prompt = <<<PROMPT
Analisa mood customer dari input berikut dan berikan response dalam format JSON:

Input Customer: "{$customerInput}"

Response harus dalam format JSON dengan struktur:
{
    "detected_mood": "happy/sad/stressed/relaxed/energetic/calm",
    "confidence": 0.85,
    "reasoning": "Penjelasan singkat kenapa mood ini terdeteksi",
    "suggested_mood_tags": ["happy", "energetic"]
}

Hanya return JSON, tidak ada text lain.
PROMPT;

        $result = $this->chat($prompt);

        if ($result && $result['success']) {
            try {
                // Parse JSON response - handle case jika AI return text + JSON
                $content = trim($result['content']);
                
                // Extract JSON jika ada text tambahan
                if (preg_match('/\{.*\}/s', $content, $matches)) {
                    $jsonString = $matches[0];
                } else {
                    $jsonString = $content;
                }
                
                $moodData = json_decode($jsonString, true);
                
                if (json_last_error() === JSON_ERROR_NONE && isset($moodData['detected_mood'])) {
                    return $moodData;
                }
                
                Log::warning('AI returned non-JSON mood analysis', ['content' => $content]);
                return null;
                
            } catch (Exception $e) {
                Log::error('Failed to parse mood analysis: ' . $e->getMessage(), [
                    'content' => $result['content'] ?? 'no content'
                ]);
                return null;
            }
        }

        return null;
    }

    /**
     * Dapatkan rekomendasi menu berdasarkan mood
     * 
     * @param string $mood Mood customer
     * @param array $availableMenus Menu yang tersedia
     * @return array|null Rekomendasi menu
     */
    public function recommendMenuByMood(string $mood, array $availableMenus): ?array
    {
        $menuList = json_encode($availableMenus, JSON_PRETTY_PRINT);

        $prompt = <<<PROMPT
Rekomendasi 3 menu kopi terbaik untuk customer dengan mood: "{$mood}"

Menu yang tersedia:
{$menuList}

Response dalam format JSON:
{
    "recommendations": [
        {
            "menu_id": 1,
            "name": "Espresso",
            "match_score": 0.95,
            "reason": "Kenapa menu ini cocok untuk mood customer"
        }
    ],
    "general_advice": "Saran umum untuk customer"
}

Hanya return JSON, tidak ada text lain.
PROMPT;

        $result = $this->chat($prompt);

        if ($result && $result['success']) {
            try {
                $recommendations = json_decode($result['content'], true);
                return $recommendations;
            } catch (Exception $e) {
                Log::error('Failed to parse recommendations: ' . $e->getMessage());
                return null;
            }
        }

        return null;
    }

    /**
     * Generate response untuk chatbot
     * 
     * @param string $userMessage Pesan dari user
     * @param array $conversationHistory History percakapan sebelumnya (opsional)
     * @return string|null Response dari AI
     */
    public function generateChatResponse(string $userMessage, array $conversationHistory = []): ?string
    {
        $contextPrompt = "Percakapan sebelumnya:\n";
        foreach ($conversationHistory as $msg) {
            $contextPrompt .= "{$msg['role']}: {$msg['content']}\n";
        }

        $prompt = <<<PROMPT
{$contextPrompt}

User: {$userMessage}

Kamu adalah AI assistant MoodBrew yang friendly dan helpful. Bantu customer dengan:
- Menjawab pertanyaan tentang menu
- Memberikan rekomendasi berdasarkan mood
- Menjelaskan flavor profile kopi
- Membantu proses order

Response harus natural, friendly, dan informatif. Maksimal 2-3 kalimat.
PROMPT;

        $result = $this->chat($prompt);

        if ($result && $result['success']) {
            return $result['content'];
        }

        return null;
    }

    /**
     * Check apakah AI service aktif
     * 
     * @return bool
     */
    public function isEnabled(): bool
    {
        return $this->enabled && !empty($this->apiKey);
    }

    /**
     * Test koneksi ke Kolosal AI
     * 
     * @return array Status koneksi
     */
    public function testConnection(): array
    {
        try {
            $result = $this->chat('Hello, this is a test message.');

            return [
                'success' => $result !== null && $result['success'],
                'message' => $result ? 'Connection successful' : 'Connection failed',
                'api_url' => $this->apiUrl,
                'model' => $this->model,
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Connection error: ' . $e->getMessage(),
                'api_url' => $this->apiUrl,
                'model' => $this->model,
            ];
        }
    }
}
