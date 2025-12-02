<?php

namespace App\Services;

use App\Models\FlashSale;
use App\Models\MenuItem;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

/**
 * Flash Sale Service
 * 
 * Handle business logic untuk Dead-hour Flash Sales feature.
 * AI copywriting integration handled separately.
 */
class FlashSaleService
{
    /**
     * Get all flash sales dengan pagination
     *
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function getAllFlashSales(int $perPage = 10): LengthAwarePaginator
    {
        return FlashSale::with('triggeredBy')
            ->latest()
            ->paginate($perPage);
    }

    /**
     * Get active flash sale
     *
     * @return FlashSale|null
     */
    public function getActiveFlashSale(): ?FlashSale
    {
        return FlashSale::where('is_active', true)
            ->where('starts_at', '<=', now())
            ->where(function ($query) {
                $query->whereNull('ends_at')
                    ->orWhere('ends_at', '>', now());
            })
            ->first();
    }

    /**
     * Create new flash sale
     *
     * @param array $data
     * @return FlashSale
     */
    public function createFlashSale(array $data): FlashSale
    {
        // Generate promo code if not provided
        $promoCode = $data['promo_code'] ?? $this->generatePromoCode();

        return FlashSale::create([
            'name' => $data['name'],
            'promo_code' => $promoCode,
            'discount_percentage' => $data['discount_percentage'],
            'trigger_reason' => $data['trigger_reason'] ?? 'manual',
            'triggered_by' => auth()->id(),
            'starts_at' => $data['starts_at'] ?? now(),
            'ends_at' => $data['ends_at'] ?? now()->addHours(2),
            'is_active' => true,
            'ai_generated_copy' => $data['ai_generated_copy'] ?? null,
        ]);
    }

    /**
     * End flash sale
     *
     * @param int $flashSaleId
     * @return FlashSale
     */
    public function endFlashSale(int $flashSaleId): FlashSale
    {
        $flashSale = FlashSale::findOrFail($flashSaleId);

        $flashSale->update([
            'is_active' => false,
            'ends_at' => now(),
        ]);

        return $flashSale->fresh();
    }

    /**
     * Check if promo code is valid
     *
     * @param string $promoCode
     * @return FlashSale|null
     */
    public function validatePromoCode(string $promoCode): ?FlashSale
    {
        return FlashSale::where('promo_code', strtoupper($promoCode))
            ->where('is_active', true)
            ->where('starts_at', '<=', now())
            ->where(function ($query) {
                $query->whereNull('ends_at')
                    ->orWhere('ends_at', '>', now());
            })
            ->first();
    }

    /**
     * Generate unique promo code
     *
     * @return string
     */
    protected function generatePromoCode(): string
    {
        $prefix = 'BREW';
        $random = strtoupper(Str::random(4));
        $code = "{$prefix}{$random}";

        while (FlashSale::where('promo_code', $code)->exists()) {
            $random = strtoupper(Str::random(4));
            $code = "{$prefix}{$random}";
        }

        return $code;
    }

    /**
     * Get flash sale suggestions based on current conditions
     * (Placeholder untuk AI integration)
     *
     * @return array
     */
    public function getFlashSaleSuggestions(): array
    {
        // TODO: AI akan menganalisis kondisi cafe dan suggest flash sale
        // Untuk sekarang return static suggestions

        $hour = now()->hour;

        $suggestions = [];

        // Dead hour detection (jam sepi)
        if ($hour >= 14 && $hour <= 16) {
            $suggestions[] = [
                'reason' => 'dead_hour_afternoon',
                'message' => 'Jam sepi sore hari terdeteksi (14:00-16:00)',
                'suggested_discount' => 20,
                'suggested_duration' => 2,
            ];
        }

        if ($hour >= 10 && $hour <= 11) {
            $suggestions[] = [
                'reason' => 'dead_hour_morning',
                'message' => 'Jam sepi pagi terdeteksi (10:00-11:00)',
                'suggested_discount' => 15,
                'suggested_duration' => 1,
            ];
        }

        return $suggestions;
    }

    /**
     * Get flash sale statistics
     *
     * @return array
     */
    public function getFlashSaleStats(): array
    {
        $today = today();

        return [
            'totalToday' => FlashSale::whereDate('created_at', $today)->count(),
            'activeNow' => FlashSale::where('is_active', true)->count(),
            'totalThisMonth' => FlashSale::whereMonth('created_at', now()->month)->count(),
        ];
    }
}
