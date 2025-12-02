# MoodBrew AI Services

Dokumentasi untuk integrasi AI di MoodBrew.

## 📁 Structure

```
app/Services/AI/
├── AiRecommendationService.php  # Mood-based menu recommendation
├── AiChatService.php            # Conversational chatbot
├── AiCopywritingService.php     # Flash sale & promo copywriting
├── AiSentimentService.php       # Sentiment analysis & moderation
└── README.md                    # This file
```

## 🚀 Quick Start

### 1. Setup Environment Variables

Tambahkan ke file `.env`:

```env
# AI Provider: openai, groq, gemini
AI_PROVIDER=groq

# API Key dari provider
AI_API_KEY=your_api_key_here

# API URL (sesuaikan dengan provider)
AI_API_URL=https://api.groq.com/openai/v1

# Model yang digunakan
AI_MODEL=llama-3.1-70b-versatile

# Settings
AI_TEMPERATURE=0.7
AI_MAX_TOKENS=500
AI_ENABLED=true
AI_FALLBACK_ENABLED=true
```

### 2. Get API Key

#### Groq (Recommended - GRATIS!)

1. Buka https://console.groq.com
2. Sign up / Login
3. Buat API Key di Settings
4. Set `AI_PROVIDER=groq` di .env

#### OpenAI

1. Buka https://platform.openai.com
2. Sign up / Login
3. Buat API Key
4. Set `AI_PROVIDER=openai` di .env

#### Google Gemini

1. Buka https://ai.google.dev
2. Get API Key
3. Set `AI_PROVIDER=gemini` di .env

## 📖 Usage

### Mood Recommendation

```php
use App\Services\AI\AiRecommendationService;

$service = app(AiRecommendationService::class);

$result = $service->getRecommendation(
    userMood: "Saya lelah dan butuh energi",
    userId: auth()->id(), // optional
    sessionId: session()->getId() // optional
);

// Result:
// [
//     'success' => true,
//     'message' => 'AI response message',
//     'recommendations' => [MenuItem, MenuItem, MenuItem],
//     'prompt_id' => 123
// ]
```

### Chat

```php
use App\Services\AI\AiChatService;

$service = app(AiChatService::class);

$result = $service->chat(
    message: "Apa yang cocok untuk sore hari?",
    conversationHistory: [], // previous messages
    userId: null,
    sessionId: session()->getId()
);

// Result:
// [
//     'success' => true,
//     'message' => 'AI chat response',
//     'suggested_menus' => [...],
//     'actions' => [...]
// ]
```

### Copywriting

```php
use App\Services\AI\AiCopywritingService;

$service = app(AiCopywritingService::class);

// Flash Sale Copy
$copy = $service->generateFlashSaleCopy($flashSale, 'urgent');
// Returns: headline, description, cta, hashtags

// Menu Description
$desc = $service->generateMenuDescription($menuItem);

// Promo Message
$msg = $service->generatePromoMessage('rainy_day', $featuredMenus);
```

### Sentiment Analysis

```php
use App\Services\AI\AiSentimentService;

$service = app(AiSentimentService::class);

// Analyze text
$result = $service->analyzeSentiment("Kopinya enak banget!");
// Returns: score, label, emotions, summary

// Analyze Vibe Entry
$vibeEntry = $service->analyzeVibeEntry($vibeEntry);

// Content Moderation
$moderation = $service->moderateContent($userInput);
// Returns: is_appropriate, reason, confidence

// Customer Mood Summary (for Empathy Radar)
$moodSummary = $service->getCustomerMoodSummary($userId, $sessionId);
```

## 🔧 Implementation Guide

### Step 1: Implement `callAiApi()` Method

Setiap service memiliki method `callAiApi()` yang perlu diimplementasi.

Contoh untuk **Groq**:

```php
protected function callAiApi(string $prompt): string
{
    $response = Http::withHeaders([
        'Authorization' => 'Bearer ' . $this->apiKey,
        'Content-Type' => 'application/json',
    ])->post($this->apiUrl . '/chat/completions', [
        'model' => $this->model,
        'messages' => [
            ['role' => 'user', 'content' => $prompt]
        ],
        'temperature' => config('services.ai.temperature', 0.7),
        'max_tokens' => config('services.ai.max_tokens', 500),
    ]);

    if ($response->failed()) {
        throw new \Exception('AI API Error: ' . $response->body());
    }

    return $response->json('choices.0.message.content');
}
```

Contoh untuk **OpenAI** (sama seperti Groq, hanya beda URL):

```php
// URL: https://api.openai.com/v1/chat/completions
// Model: gpt-3.5-turbo atau gpt-4
```

Contoh untuk **Gemini**:

```php
protected function callAiApi(string $prompt): string
{
    $response = Http::post(
        $this->apiUrl . '/models/' . $this->model . ':generateContent?key=' . $this->apiKey,
        [
            'contents' => [
                ['parts' => [['text' => $prompt]]]
            ],
            'generationConfig' => [
                'temperature' => config('services.ai.temperature', 0.7),
                'maxOutputTokens' => config('services.ai.max_tokens', 500),
            ]
        ]
    );

    return $response->json('candidates.0.content.parts.0.text');
}
```

### Step 2: Create Base AI Service (Optional)

Untuk DRY code, buat base service:

```php
// app/Services/AI/BaseAiService.php

namespace App\Services\AI;

use Illuminate\Support\Facades\Http;

abstract class BaseAiService
{
    protected string $apiKey;
    protected string $apiUrl;
    protected string $model;
    protected string $provider;

    public function __construct()
    {
        $this->provider = config('services.ai.provider', 'groq');
        $this->apiKey = config('services.ai.api_key', '');
        $this->apiUrl = config('services.ai.api_url');
        $this->model = config('services.ai.model');
    }

    protected function callAiApi(string $prompt): string
    {
        return match ($this->provider) {
            'openai', 'groq' => $this->callOpenAiCompatible($prompt),
            'gemini' => $this->callGemini($prompt),
            default => throw new \Exception("Unknown AI provider: {$this->provider}"),
        };
    }

    protected function callOpenAiCompatible(string $prompt): string
    {
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $this->apiKey,
            'Content-Type' => 'application/json',
        ])->post($this->apiUrl . '/chat/completions', [
            'model' => $this->model,
            'messages' => [
                ['role' => 'user', 'content' => $prompt]
            ],
            'temperature' => config('services.ai.temperature', 0.7),
            'max_tokens' => config('services.ai.max_tokens', 500),
        ]);

        if ($response->failed()) {
            throw new \Exception('AI API Error: ' . $response->body());
        }

        return $response->json('choices.0.message.content');
    }

    protected function callGemini(string $prompt): string
    {
        // Implement Gemini API call
    }
}
```

## 📊 Data Flow

```
Customer Input (mood)
        ↓
AiRecommendationService
        ↓
┌───────────────────────┐
│ 1. Build Menu Context │ ← MenuItem::toAiContext()
│ 2. Build Prompt       │
│ 3. Call AI API        │
│ 4. Parse Response     │
│ 5. Save to DB         │ → mood_prompts table
└───────────────────────┘
        ↓
Return Recommendations
        ↓
Display to Customer
```

## 🎯 Available Data for AI

### Menu Items

```php
$menuItem->toAiContext();
// Output: "[coffee] Espresso Shot - Rp 25.000 | Flavor: acidity: 4/5, body: 5/5, mood tags: [energizing, bold]"
```

### Flavor Profile (JSON)

```json
{
    "sweetness": 3,
    "bitterness": 4,
    "strength": 5,
    "flavor_notes": ["bold", "energizing", "smoky"]
}
```

### Customer Data

-   `mood_prompts`: History percakapan dengan AI
-   `vibe_entries`: Posts di Silent Social Wall
-   `orders.customer_mood_summary`: Mood summary saat order

## 🧪 Testing

Semua service sudah include mock responses untuk development.
Set `AI_ENABLED=false` di .env untuk menggunakan mock data.

## 📝 Notes

1. **Rate Limiting**: Implement rate limiting untuk API calls
2. **Caching**: Cache menu context untuk mengurangi API calls
3. **Error Handling**: Selalu ada fallback jika AI gagal
4. **Logging**: Log semua AI interactions untuk debugging
5. **Cost**: Monitor API usage, terutama untuk OpenAI

## 🔗 References

-   [OpenAI API Docs](https://platform.openai.com/docs)
-   [Groq API Docs](https://console.groq.com/docs)
-   [Gemini API Docs](https://ai.google.dev/docs)
-   [Laravel HTTP Client](https://laravel.com/docs/http-client)
