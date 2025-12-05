# Panduan Integrasi Kolosal AI di MoodBrew

## 🚀 Setup Awal

### 1. Tambahkan API Key ke File `.env`

Buka file `.env` di root project, lalu tambahkan konfigurasi berikut:

```env
# AI Configuration - Kolosal AI
AI_PROVIDER=kolosal
AI_API_KEY=sk-your-actual-kolosal-api-key-here
AI_API_URL=https://api.kolosal.ai/v1
AI_MODEL=default
AI_TEMPERATURE=0.7
AI_MAX_TOKENS=500
AI_ENABLED=true
AI_FALLBACK_ENABLED=true
```

**Ganti `sk-your-actual-kolosal-api-key-here` dengan API key asli dari Kolosal AI!**

### 2. Sesuaikan dengan Dokumentasi Kolosal AI

Buka file `app/Services/KolosalAIService.php` dan sesuaikan:

- **API Endpoint**: Periksa dokumentasi Kolosal untuk endpoint yang benar
- **Request Format**: Sesuaikan struktur `$payload` dengan format yang diterima Kolosal
- **Response Format**: Sesuaikan parsing response dengan struktur yang dikembalikan Kolosal

**Contoh yang mungkin perlu disesuaikan:**

```php
// Di method chat(), baris ~45
$response = Http::timeout(30)
    ->withHeaders([
        'Authorization' => 'Bearer ' . $this->apiKey,
        'Content-Type' => 'application/json',
        // Tambahkan header lain jika diperlukan oleh Kolosal
    ])
    ->post($this->apiUrl . '/chat/completions', $payload);
```

---

## 📋 Cara Menggunakan AI Service

### Opsi 1: Via API (Recommended untuk Frontend/Mobile)

#### Test Koneksi
```bash
GET http://127.0.0.1:8000/api/ai/test
```

Response:
```json
{
    "success": true,
    "message": "Connection successful",
    "api_url": "https://api.kolosal.ai/v1",
    "model": "default"
}
```

#### Analisa Mood Customer
```bash
POST http://127.0.0.1:8000/api/ai/analyze-mood
Content-Type: application/json

{
    "message": "I'm feeling really stressed today, need something to calm me down"
}
```

Response:
```json
{
    "success": true,
    "data": {
        "detected_mood": "stressed",
        "confidence": 0.92,
        "reasoning": "Customer explicitly mentioned feeling stressed and wanting to calm down",
        "suggested_mood_tags": ["stressed", "calm"]
    }
}
```

#### Rekomendasi Menu
```bash
POST http://127.0.0.1:8000/api/ai/recommend
Content-Type: application/json

{
    "mood": "happy"
}
```

Response:
```json
{
    "success": true,
    "data": {
        "recommendations": [
            {
                "menu_id": 3,
                "name": "Caramel Macchiato",
                "match_score": 0.95,
                "reason": "Sweet and smooth, perfect for happy mood"
            }
        ],
        "general_advice": "Enjoy something sweet to complement your happiness!",
        "source": "ai"
    }
}
```

#### Chat dengan AI Assistant
```bash
POST http://127.0.0.1:8000/api/ai/chat
Content-Type: application/json

{
    "message": "What coffee is good for morning energy?",
    "conversation_history": []
}
```

Response:
```json
{
    "success": true,
    "data": {
        "response": "For morning energy, I'd recommend our Espresso or Americano! Both have strong caffeine content to kickstart your day. Would you like to know more about their flavor profiles?",
        "timestamp": "2025-12-05T10:30:00Z"
    }
}
```

---

### Opsi 2: Via Service Class (Dalam Controller/Service Lain)

#### Inject Service di Constructor

```php
use App\Services\KolosalAIService;

class YourController extends Controller
{
    protected KolosalAIService $aiService;

    public function __construct(KolosalAIService $aiService)
    {
        $this->aiService = $aiService;
    }

    public function someMethod()
    {
        // Gunakan $this->aiService
    }
}
```

#### Contoh Penggunaan di Controller

```php
// 1. Test Koneksi
public function testAI()
{
    $test = $this->aiService->testConnection();
    
    if ($test['success']) {
        return 'AI Connected!';
    }
    return 'AI Failed: ' . $test['message'];
}

// 2. Analisa Mood
public function analyzeMood(Request $request)
{
    $moodData = $this->aiService->analyzeMood($request->customer_message);
    
    if ($moodData) {
        $detectedMood = $moodData['detected_mood'];
        $confidence = $moodData['confidence'];
        
        // Gunakan hasil analisa
        return "Detected mood: {$detectedMood} (confidence: {$confidence})";
    }
    
    return 'Failed to analyze mood';
}

// 3. Rekomendasi Menu
public function getRecommendation(string $mood)
{
    $menuItems = MenuItem::with('category')->get();
    
    $availableMenus = $menuItems->map(fn($item) => [
        'id' => $item->id,
        'name' => $item->name,
        'description' => $item->description,
        'price' => $item->price,
        'mood_tags' => $item->mood_tags,
    ])->toArray();
    
    $recommendations = $this->aiService->recommendMenuByMood($mood, $availableMenus);
    
    return view('recommendations', compact('recommendations'));
}

// 4. Chat Response
public function chat(Request $request)
{
    $history = session('chat_history', []);
    
    $response = $this->aiService->generateChatResponse(
        $request->message,
        $history
    );
    
    // Simpan ke history
    $history[] = ['role' => 'user', 'content' => $request->message];
    $history[] = ['role' => 'assistant', 'content' => $response];
    session(['chat_history' => $history]);
    
    return $response;
}

// 5. Custom AI Call
public function customAICall()
{
    $prompt = "Berikan 5 rekomendasi nama menu kopi yang unik dan menarik";
    
    $result = $this->aiService->chat($prompt);
    
    if ($result && $result['success']) {
        $aiResponse = $result['content'];
        return $aiResponse;
    }
    
    return 'AI service unavailable';
}
```

---

## 🎯 Use Cases di MoodBrew

### 1. **Mood-Based Recommendation**
Di halaman menu customer, gunakan AI untuk memberikan rekomendasi yang lebih personal:

```php
// CustomerMenuController.php
public function index(Request $request)
{
    if ($request->has('mood_input')) {
        // Analisa mood dari input customer
        $moodData = $this->aiService->analyzeMood($request->mood_input);
        
        if ($moodData) {
            $mood = $moodData['detected_mood'];
            
            // Dapatkan rekomendasi dari AI
            $menuItems = $this->menuService->getMenuItems();
            $recommendations = $this->aiService->recommendMenuByMood(
                $mood, 
                $menuItems->toArray()
            );
            
            return view('customer.menu.recommendations', compact('recommendations'));
        }
    }
    
    // Default menu list
    return view('customer.menu.index');
}
```

### 2. **AI Chatbot di Vibe Wall**
Customer bisa chat dengan AI untuk bertanya tentang menu:

```php
// CustomerVibeWallController.php
public function aiChat(Request $request)
{
    $response = $this->aiService->generateChatResponse(
        $request->message,
        session('vibe_chat_history', [])
    );
    
    return response()->json(['response' => $response]);
}
```

### 3. **Smart Menu Description Generator (Admin)**
Admin bisa generate deskripsi menu dengan AI:

```php
// AdminMenuController.php
public function generateDescription($menuId)
{
    $menu = MenuItem::findOrFail($menuId);
    
    $prompt = "Buatkan deskripsi menarik untuk menu kopi: {$menu->name}. 
               Flavor profile: acidity {$menu->flavor_profile['acidity']}, 
               body {$menu->flavor_profile['body']}.
               Maksimal 2 kalimat, fokus pada taste dan mood yang cocok.";
    
    $result = $this->aiService->chat($prompt);
    
    if ($result && $result['success']) {
        $menu->description = $result['content'];
        $menu->save();
    }
}
```

---

## 🔧 Troubleshooting

### API Key Error
**Error**: "AI API Key tidak ditemukan di .env"

**Solusi**: 
1. Pastikan file `.env` sudah di-update
2. Restart Laravel dengan `php artisan config:clear`

### Connection Timeout
**Error**: "Connection timeout"

**Solusi**:
1. Periksa API URL di `.env` sudah benar
2. Tingkatkan timeout di `KolosalAIService.php`: `Http::timeout(60)`

### Invalid Response Format
**Error**: "Failed to parse JSON"

**Solusi**:
Sesuaikan parsing response dengan format dari Kolosal AI. Lihat di method `chat()` dan sesuaikan:

```php
// Sesuaikan sesuai struktur response Kolosal
return [
    'success' => true,
    'content' => $data['choices'][0]['message']['content'] ?? '',
    'raw_response' => $data,
];
```

---

## 📝 Testing dengan Postman/Thunder Client

1. **Install Thunder Client extension** di VS Code
2. **Import collection** atau buat request manual
3. **Test koneksi** dulu dengan endpoint `/api/ai/test`
4. **Coba fitur** lainnya setelah koneksi berhasil

---

## ✅ Checklist Setup

- [ ] Copy API key dari dashboard Kolosal AI
- [ ] Update file `.env` dengan API key
- [ ] Jalankan `php artisan config:clear`
- [ ] Sesuaikan `KolosalAIService.php` dengan dokumentasi Kolosal
- [ ] Test koneksi via API: `GET /api/ai/test`
- [ ] Coba analyze mood: `POST /api/ai/analyze-mood`
- [ ] Coba rekomendasi: `POST /api/ai/recommend`
- [ ] Implementasikan di UI customer

---

## 🎓 Tips

1. **Fallback Strategy**: Selalu sediakan fallback jika AI gagal (sudah diimplementasikan)
2. **Logging**: Periksa `storage/logs/laravel.log` untuk debug
3. **Rate Limiting**: Tambahkan rate limiting jika diperlukan
4. **Caching**: Cache hasil AI untuk query yang sama
5. **Prompt Engineering**: Tuning prompt untuk hasil yang lebih baik

---

**Butuh bantuan lebih lanjut? Silakan cek dokumentasi Kolosal AI atau tanya!** 🚀
