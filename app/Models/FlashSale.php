<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Carbon\Carbon;

/**
 * FlashSale Model - Promo dadakan yang di-trigger saat dead-hour
 * 
 * @property int $id
 * @property string $name
 * @property string $promo_code
 * @property int $discount_percentage
 * @property string|null $trigger_reason
 * @property int|null $triggered_by
 * @property string|null $ai_generated_copy
 * @property bool $is_active
 * @property \DateTime|null $starts_at
 * @property \DateTime|null $ends_at
 * @property \DateTime $created_at
 * @property \DateTime $updated_at
 */
class FlashSale extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'promo_code',
        'discount_percentage',
        'trigger_reason',
        'triggered_by',
        'ai_generated_copy',
        'is_active',
        'starts_at',
        'ends_at',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'discount_percentage' => 'integer',
            'is_active' => 'boolean',
            'starts_at' => 'datetime',
            'ends_at' => 'datetime',
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    /**
     * User yang trigger flash sale
     */
    public function triggeredBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'triggered_by');
    }

    /**
     * Orders yang menggunakan flash sale ini
     */
    public function orders(): HasMany
    {
        return $this->hasMany(Order::class, 'applied_flash_sale_id');
    }

    /*
    |--------------------------------------------------------------------------
    | Scopes
    |--------------------------------------------------------------------------
    */

    /**
     * Scope untuk flash sale yang sedang aktif
     */
    public function scopeActive($query)
    {
        $now = Carbon::now();

        return $query->where('is_active', true)
            ->where(function ($q) use ($now) {
                $q->whereNull('starts_at')
                    ->orWhere('starts_at', '<=', $now);
            })
            ->where(function ($q) use ($now) {
                $q->whereNull('ends_at')
                    ->orWhere('ends_at', '>=', $now);
            });
    }

    /*
    |--------------------------------------------------------------------------
    | Helper Methods
    |--------------------------------------------------------------------------
    */

    /**
     * Check apakah flash sale ini currently valid
     */
    public function isCurrentlyValid(): bool
    {
        if (!$this->is_active) {
            return false;
        }

        $now = Carbon::now();

        if ($this->starts_at && $now->lt($this->starts_at)) {
            return false;
        }

        if ($this->ends_at && $now->gt($this->ends_at)) {
            return false;
        }

        return true;
    }

    /**
     * Calculate discount amount dari harga asli
     */
    public function calculateDiscount(float $originalPrice): float
    {
        return $originalPrice * ($this->discount_percentage / 100);
    }

    /**
     * Calculate final price setelah discount
     */
    public function calculateFinalPrice(float $originalPrice): float
    {
        return $originalPrice - $this->calculateDiscount($originalPrice);
    }

    /**
     * Activate flash sale
     */
    public function activate(): void
    {
        $this->update(['is_active' => true]);
    }

    /**
     * Deactivate flash sale
     */
    public function deactivate(): void
    {
        $this->update(['is_active' => false]);
    }

    /**
     * Get remaining time untuk flash sale (dalam detik)
     */
    public function getRemainingSeconds(): ?int
    {
        if (!$this->ends_at) {
            return null;
        }

        $now = Carbon::now();

        if ($now->gt($this->ends_at)) {
            return 0;
        }

        return $now->diffInSeconds($this->ends_at);
    }
}
