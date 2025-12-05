<?php

namespace App\Services\AI;

use App\Models\FlashSale;
use App\Models\MenuItem;
use App\Services\KolosalAIService;
use Illuminate\Support\Facades\Log;

/**
 * AI Copywriting Service
 * 
 * Service untuk generate copywriting otomatis:
 * - Flash Sale announcements
 * - Menu descriptions
 * - Promotional messages
 * 
 * @author [Nama Teman Anda]
 */
class AiCopywritingService
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
     * Generate copywriting untuk Flash Sale
     * 
     * @param string $name Nama promo
     * @param int $discountPercentage Persentase diskon
     * @param int $durationMinutes Durasi dalam menit
     * @param string $triggerReason Alasan trigger (optional)
     * @param string $style Style copywriting: urgent, playful, elegant
     * @return array Array dengan keys: headline, description, cta, hashtags
     */
    public function generateFlashSaleCopy(
        string $name,
        int $discountPercentage,
        int $durationMinutes,
        string $triggerReason = '',
        string $style = 'urgent'
    ): array {
        $prompt = $this->buildFlashSalePrompt($name, $discountPercentage, $durationMinutes, $triggerReason, $style);
        $response = $this->callAiApi($prompt);

        return $this->parseFlashSaleCopy($response);
    }

    /**
     * Generate deskripsi menu yang menarik
     * 
     * @param MenuItem $menuItem
     * @return string
     */
    public function generateMenuDescription(MenuItem $menuItem): string
    {
        $name = $menuItem->name;
        $category = $menuItem->category;
        $price = $menuItem->price;
        $context = $menuItem->toAiContext();

        $prompt = 'Buat deskripsi menu yang menarik dan appetizing untuk:' . "\n\n";
        $prompt .= 'Nama: ' . $name . "\n";
        $prompt .= 'Kategori: ' . $category . "\n";
        $prompt .= 'Harga: Rp ' . $price . "\n";
        $prompt .= 'Flavor Profile: ' . $context . "\n\n";
        $prompt .= 'Instruksi:' . "\n";
        $prompt .= '- Gunakan bahasa Indonesia yang menarik' . "\n";
        $prompt .= '- Maksimal 2 kalimat' . "\n";
        $prompt .= '- Highlight flavor dan experience' . "\n";
        $prompt .= '- Buat pembaca ingin mencoba' . "\n\n";
        $prompt .= 'Deskripsi:';

        return $this->callAiApi($prompt);
    }

    /**
     * Generate promotional message untuk customer
     * 
     * @param string $occasion Contoh: 'morning', 'rainy_day', 'weekend'
     * @param array $featuredMenus Menu yang ingin dipromosikan
     * @return string
     */
    public function generatePromoMessage(string $occasion, array $featuredMenus = []): string
    {
        $menuNames = collect($featuredMenus)->pluck('name')->join(', ');

        $occasions = [
            'morning' => 'pagi hari yang produktif',
            'rainy_day' => 'hari hujan yang cozy',
            'weekend' => 'weekend santai',
            'afternoon' => 'sore yang hangat',
            'late_night' => 'malam yang tenang',
        ];

        $context = $occasions[$occasion] ?? 'hari yang spesial';

        $prompt = 'Buat pesan promosi singkat untuk ' . $context . '.' . "\n\n";
        $prompt .= 'Menu yang ingin dipromosikan: ' . $menuNames . "\n\n";
        $prompt .= 'Instruksi:' . "\n";
        $prompt .= '- Maksimal 2 kalimat' . "\n";
        $prompt .= '- Gunakan emoji yang relevan' . "\n";
        $prompt .= '- Buat personal dan relatable' . "\n";
        $prompt .= '- Bahasa Indonesia casual' . "\n\n";
        $prompt .= 'Pesan:';

        return $this->callAiApi($prompt);
    }

    /**
     * Generate Vibe Wall comment response
     * Untuk auto-reply di Silent Social Wall
     * 
     * @param string $vibeContent Konten dari customer
     * @param float $sentimentScore Sentiment score (-1 to 1)
     * @return string
     */
    public function generateVibeResponse(string $vibeContent, float $sentimentScore): string
    {
        $sentiment = match (true) {
            $sentimentScore > 0.3 => 'positive',
            $sentimentScore < -0.3 => 'negative',
            default => 'neutral',
        };

        $prompt = 'Customer menulis di Vibe Wall: "' . $vibeContent . '"' . "\n";
        $prompt .= 'Sentiment: ' . $sentiment . "\n\n";
        $prompt .= 'Buat response singkat yang empati dari MoodBrew sebagai cafe.' . "\n\n";
        $prompt .= 'Instruksi:' . "\n";
        $prompt .= '- Maksimal 1-2 kalimat' . "\n";
        $prompt .= '- Jika positive: apresiasi dan semangat' . "\n";
        $prompt .= '- Jika negative: empati dan support' . "\n";
        $prompt .= '- Jika neutral: friendly acknowledgment' . "\n";
        $prompt .= '- Gunakan emoji yang sesuai' . "\n\n";
        $prompt .= 'Response:';

        return $this->callAiApi($prompt);
    }

    /**
     * Build prompt untuk Flash Sale copywriting
     */
    protected function buildFlashSalePrompt(
        string $name,
        int $discountPercentage,
        int $durationMinutes,
        string $triggerReason,
        string $style
    ): string {
        $styleGuide = match ($style) {
            'urgent' => 'Urgen, FOMO, countdown vibe. Gunakan caps lock untuk emphasis.',
            'playful' => 'Fun, casual, friendly. Gunakan banyak emoji.',
            'elegant' => 'Sophisticated, premium feel. Minimal emoji.',
            default => 'Balanced, informative tapi menarik.',
        };

        $durationHours = round($durationMinutes / 60, 1);
        $durationText = $durationHours >= 1 
            ? $durationHours . ' jam'
            : $durationMinutes . ' menit';

        $prompt = 'Buat copywriting Flash Sale untuk cafe:' . "\n\n";
        $prompt .= 'DETAIL PROMO:' . "\n";
        $prompt .= '- Nama: ' . $name . "\n";
        $prompt .= '- Diskon: ' . $discountPercentage . '%' . "\n";
        $prompt .= '- Durasi: ' . $durationText . "\n";
        if (!empty($triggerReason)) {
            $prompt .= '- Alasan: ' . $triggerReason . "\n";
        }
        $prompt .= "\n";
        $prompt .= 'STYLE GUIDE: ' . $styleGuide . "\n\n";
        $prompt .= 'OUTPUT FORMAT (JSON):' . "\n";
        $prompt .= '{' . "\n";
        $prompt .= '    "headline": "Headline utama (max 10 kata)",' . "\n";
        $prompt .= '    "description": "Deskripsi singkat (max 30 kata)",' . "\n";
        $prompt .= '    "cta": "Call to action (max 3 kata)",' . "\n";
        $prompt .= '    "hashtags": ["hashtag1", "hashtag2"]' . "\n";
        $prompt .= '}' . "\n\n";
        $prompt .= 'Response:';

        return $prompt;
    }

    /**
     * Call AI API
     * 
     * @param string $prompt
     * @return string
     */
    protected function callAiApi(string $prompt): string
    {
        try {
            $result = $this->kolosalAI->chat($prompt);
            
            if ($result && $result['success']) {
                return $result['content'];
            }
            
            Log::warning('KolosalAI copywriting failed, using fallback');
            return $this->getFallbackResponse($prompt);
        } catch (\Exception $e) {
            Log::error('AI Copywriting error: ' . $e->getMessage());
            return $this->getFallbackResponse($prompt);
        }
    }

    /**
     * Get fallback response when AI fails
     */
    protected function getFallbackResponse(string $prompt): string
    {
        // Mock response untuk Flash Sale
        if (str_contains($prompt, 'Flash Sale')) {
            return json_encode([
                'headline' => '⚡ FLASH SALE ALERT! Diskon Spesial Cuma Sekarang!',
                'description' => 'Nikmati diskon hingga 30% untuk semua menu favorit. Jangan sampai kehabisan!',
                'cta' => 'ORDER NOW',
                'hashtags' => ['#MoodBrewFlashSale', '#PromoKopi', '#DiskonCafe'],
            ]);
        }

        // Mock response untuk menu description
        if (str_contains($prompt, 'deskripsi menu')) {
            return 'Perpaduan sempurna antara espresso bold dengan susu creamy, menciptakan harmoni rasa yang memanjakan lidah di setiap tegukan.';
        }

        // Mock response untuk promo message
        if (str_contains($prompt, 'pesan promosi')) {
            return '☀️ Pagi yang cerah butuh kopi yang tepat! Mulai harimu dengan Cappuccino favorit kami yang bikin semangat!';
        }

        // Mock response untuk vibe
        return '💛 Terima kasih sudah berbagi! Semoga harimu menyenangkan!';
    }

    /**
     * Parse Flash Sale copy response
     */
    protected function parseFlashSaleCopy(string $response): array
    {
        try {
            $data = json_decode($response, true);

            return [
                'headline' => $data['headline'] ?? "⚡ Flash Sale Spesial!",
                'description' => $data['description'] ?? "Diskon terbatas untuk semua menu favorit!",
                'cta' => $data['cta'] ?? 'ORDER SEKARANG',
                'hashtags' => $data['hashtags'] ?? ['#MoodBrew'],
            ];
        } catch (\Exception $e) {
            // Fallback
            return [
                'headline' => "⚡ Flash Sale Spesial!",
                'description' => "Nikmati diskon terbatas untuk semua menu. Buruan order!",
                'cta' => 'ORDER NOW',
                'hashtags' => ['#MoodBrew', '#FlashSale'],
            ];
        }
    }
}
