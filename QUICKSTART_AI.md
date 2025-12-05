# 🚀 Quick Start - Kolosal AI Integration

## ⚡ Setup dalam 3 Langkah

### 1️⃣ Tambahkan API Key ke `.env`
```env
AI_PROVIDER=kolosal
AI_API_KEY=sk-your-kolosal-api-key-here
AI_API_URL=https://api.kolosal.ai/v1
AI_MODEL=default
AI_ENABLED=true
```

### 2️⃣ Clear Config Cache
```bash
php artisan config:clear
```

### 3️⃣ Test Koneksi
```bash
# Via browser
http://127.0.0.1:8000/api/ai/test

# Via PowerShell
Invoke-WebRequest -Uri "http://127.0.0.1:8000/api/ai/test"
```

---

## 📍 Endpoints API

| Method | Endpoint | Deskripsi |
|--------|----------|-----------|
| GET | `/api/ai/test` | Test koneksi AI |
| POST | `/api/ai/analyze-mood` | Analisa mood dari text |
| POST | `/api/ai/recommend` | Rekomendasi menu by mood |
| POST | `/api/ai/chat` | Chat dengan AI assistant |

---

## 💻 Contoh Request & Response

### Test Koneksi
```bash
GET /api/ai/test
```
✅ Response:
```json
{
    "success": true,
    "message": "Connection successful"
}
```

### Analisa Mood
```bash
POST /api/ai/analyze-mood
Content-Type: application/json

{
    "message": "I'm so happy today!"
}
```
✅ Response:
```json
{
    "success": true,
    "data": {
        "detected_mood": "happy",
        "confidence": 0.95,
        "reasoning": "Explicit expression of happiness"
    }
}
```

### Rekomendasi Menu
```bash
POST /api/ai/recommend
Content-Type: application/json

{
    "mood": "happy"
}
```
✅ Response:
```json
{
    "success": true,
    "data": {
        "recommendations": [
            {
                "menu_id": 1,
                "name": "Caramel Macchiato",
                "match_score": 0.92
            }
        ]
    }
}
```

---

## 🎨 Demo Page

Akses halaman demo untuk testing:
```
http://127.0.0.1:8000/order/ai-demo
```

**Fitur di demo page:**
- ✅ AI Chatbot interaktif
- ✅ Mood analyzer dengan confidence score
- ✅ Menu recommendation berdasarkan mood
- ✅ Real-time testing semua fitur AI

---

## 🔧 Implementasi di Code

### Inject Service di Controller
```php
use App\Services\KolosalAIService;

class YourController extends Controller
{
    protected KolosalAIService $aiService;

    public function __construct(KolosalAIService $aiService)
    {
        $this->aiService = $aiService;
    }
}
```

### Gunakan AI Service
```php
// Test koneksi
$test = $this->aiService->testConnection();

// Analisa mood
$mood = $this->aiService->analyzeMood("I feel stressed");

// Rekomendasi menu
$recs = $this->aiService->recommendMenuByMood("happy", $menuData);

// Chat response
$reply = $this->aiService->generateChatResponse("Hello!");
```

---

## 📁 File-file Penting

| File | Fungsi |
|------|--------|
| `app/Services/KolosalAIService.php` | Service class utama AI |
| `app/Http/Controllers/Api/AIChatController.php` | API controller |
| `routes/api.php` | API routes |
| `resources/views/customer/ai-demo.blade.php` | Demo page |
| `config/services.php` | Konfigurasi AI |
| `.env` | API key & settings |

---

## 🐛 Troubleshooting

| Error | Solusi |
|-------|--------|
| "API Key not found" | Update `.env` dan run `php artisan config:clear` |
| "Connection timeout" | Periksa API URL, coba tingkatkan timeout |
| "Invalid response" | Sesuaikan parsing dengan dokumentasi Kolosal |
| 404 pada `/api/ai/*` | Pastikan `routes/api.php` sudah di-load |

---

## ✅ Checklist

- [ ] API key dari Kolosal AI sudah didapat
- [ ] `.env` sudah di-update dengan API key
- [ ] Config cache sudah di-clear
- [ ] Test endpoint `/api/ai/test` berhasil
- [ ] Demo page `/order/ai-demo` bisa diakses
- [ ] Sesuaikan `KolosalAIService.php` dengan dokumentasi Kolosal (jika perlu)

---

## 📖 Dokumentasi Lengkap

Baca `DOCS_AI_INTEGRATION.md` untuk:
- Setup detail step-by-step
- Semua use cases dan contoh implementasi
- Troubleshooting lengkap
- Tips & best practices

---

**Ready to go! 🎉** Tinggal masukkan API key dan test!
