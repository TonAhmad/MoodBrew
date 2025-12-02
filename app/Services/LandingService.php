<?php

namespace App\Services;

use App\Models\MenuItem;
use App\Models\FlashSale;
use Illuminate\Support\Collection;

/**
 * LandingService - Business logic untuk landing page
 * 
 * Service layer untuk memisahkan business logic dari controller
 * Sesuai clean architecture principle
 */
class LandingService
{
    /**
     * Get featured menu items untuk display di landing page
     * 
     * @param int $limit
     * @return Collection
     */
    public function getFeaturedMenuItems(int $limit = 6): Collection
    {
        return MenuItem::query()
            ->available()
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Get menu items grouped by category
     * 
     * @return Collection
     */
    public function getMenuByCategory(): Collection
    {
        return MenuItem::query()
            ->available()
            ->orderBy('category')
            ->orderBy('name')
            ->get()
            ->groupBy('category');
    }

    /**
     * Get active flash sale untuk banner di landing
     * 
     * @return FlashSale|null
     */
    public function getActiveFlashSale(): ?FlashSale
    {
        return FlashSale::query()
            ->active()
            ->orderBy('discount_percentage', 'desc')
            ->first();
    }

    /**
     * Get landing page statistics
     * 
     * @return array
     */
    public function getLandingStats(): array
    {
        return [
            'totalMenuItems' => MenuItem::query()->available()->count(),
            'categories' => MenuItem::getCategories(),
            'hasActivePromo' => FlashSale::query()->active()->exists(),
        ];
    }

    /**
     * Get data untuk hero section
     * 
     * @return array
     */
    public function getHeroData(): array
    {
        return [
            'tagline' => 'AI-Powered Coffee Experience',
            'headline' => 'Coffee yang Memahami Perasaanmu',
            'subheadline' => 'Ceritakan mood-mu hari ini, dan AI kami akan merekomendasikan minuman yang sempurna untukmu.',
        ];
    }

    /**
     * Get features list untuk features section
     * 
     * @return array
     */
    public function getFeatures(): array
    {
        return [
            [
                'icon' => '🧠',
                'title' => 'AI Mood Detection',
                'description' => 'Ceritakan perasaanmu, dan AI kami akan menganalisis mood-mu untuk merekomendasikan minuman yang paling cocok.',
            ],
            [
                'icon' => '📱',
                'title' => 'Scan & Order',
                'description' => 'Duduk di meja favoritmu, scan QR code, pilih menu atau minta rekomendasi AI, lalu bayar di kasir saat siap.',
            ],
            [
                'icon' => '⚡',
                'title' => 'Flash Sales AI',
                'description' => 'Promo dadakan dengan copywriting yang di-generate AI. Jangan lewatkan penawaran spesial di jam-jam tertentu!',
            ],
            [
                'icon' => '💭',
                'title' => 'Silent Social Wall',
                'description' => 'Bagikan vibe-mu secara anonim di wall cafe. Temukan kesamaan dengan pengunjung lain tanpa tekanan sosmed.',
            ],
            [
                'icon' => '💝',
                'title' => 'Empathy Radar',
                'description' => 'Barista kami bisa melihat mood summary kamu, sehingga bisa memberikan layanan yang lebih personal dan empati.',
            ],
            [
                'icon' => '💵',
                'title' => 'Bayar di Kasir',
                'description' => 'Tidak perlu ribet dengan pembayaran online. Pesan via app, bayar langsung di kasir dengan cash, QRIS, atau debit.',
            ],
        ];
    }

    /**
     * Get steps untuk "How it works" section
     * 
     * @return array
     */
    public function getHowItWorksSteps(): array
    {
        return [
            [
                'number' => 1,
                'title' => 'Scan QR di Meja',
                'description' => 'Duduk di meja manapun dan scan QR code untuk membuka menu digital MoodBrew.',
            ],
            [
                'number' => 2,
                'title' => 'Ceritakan Mood-mu',
                'description' => 'Ketik perasaanmu atau pilih menu langsung. AI akan memberikan rekomendasi personal.',
            ],
            [
                'number' => 3,
                'title' => 'Bayar & Nikmati',
                'description' => 'Orderan masuk ke kasir. Bayar saat siap, dan nikmati minumanmu yang diantar ke meja.',
            ],
        ];
    }
}
