<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

/**
 * MenuItem Model - Master data menu cafe
 * 
 * @property int $id
 * @property string $name
 * @property string $slug
 * @property float $price
 * @property string|null $description
 * @property string $category
 * @property int $stock_quantity
 * @property bool $is_available
 * @property array|null $flavor_profile
 * @property \DateTime $created_at
 * @property \DateTime $updated_at
 */
class MenuItem extends Model
{
    use HasFactory;

    /**
     * Category constants
     */
    public const CATEGORY_COFFEE = 'coffee';
    public const CATEGORY_NON_COFFEE = 'non-coffee';
    public const CATEGORY_PASTRY = 'pastry';
    public const CATEGORY_MAIN_COURSE = 'main_course';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'slug',
        'price',
        'description',
        'category',
        'stock_quantity',
        'is_available',
        'flavor_profile',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'stock_quantity' => 'integer',
            'is_available' => 'boolean',
            'flavor_profile' => 'array',
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | Boot Methods
    |--------------------------------------------------------------------------
    */

    protected static function boot(): void
    {
        parent::boot();

        // Auto-generate slug dari name jika tidak diisi
        static::creating(function (MenuItem $menuItem) {
            if (empty($menuItem->slug)) {
                $menuItem->slug = Str::slug($menuItem->name);
            }
        });
    }

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    /**
     * Order items yang mengandung menu ini
     */
    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    /**
     * Mood prompts yang merekomendasikan menu ini
     */
    public function moodPrompts(): HasMany
    {
        return $this->hasMany(MoodPrompt::class, 'recommended_menu_id');
    }

    /*
    |--------------------------------------------------------------------------
    | Accessors
    |--------------------------------------------------------------------------
    */

    /**
     * Get mood_tags from flavor_profile
     */
    public function getMoodTagsAttribute(): ?array
    {
        return $this->flavor_profile['mood_tags'] ?? null;
    }

    /*
    |--------------------------------------------------------------------------
    | Scopes
    |--------------------------------------------------------------------------
    */

    /**
     * Scope untuk menu yang tersedia
     */
    public function scopeAvailable($query)
    {
        return $query->where('is_available', true)
            ->where('stock_quantity', '>', 0);
    }

    /**
     * Scope untuk filter by category
     */
    public function scopeByCategory($query, string $category)
    {
        return $query->where('category', $category);
    }

    /*
    |--------------------------------------------------------------------------
    | Helper Methods
    |--------------------------------------------------------------------------
    */

    /**
     * Generate context string untuk AI prompt engineering
     * Format yang optimal untuk LLM consumption
     * 
     * @return string
     */
    public function toAiContext(): string
    {
        $flavorInfo = '';

        if (!empty($this->flavor_profile)) {
            $profile = $this->flavor_profile;
            $parts = [];

            if (isset($profile['acidity'])) {
                $parts[] = "acidity: {$profile['acidity']}/5";
            }

            if (isset($profile['body'])) {
                $parts[] = "body: {$profile['body']}/5";
            }

            if (isset($profile['mood_tags']) && is_array($profile['mood_tags'])) {
                $moodTags = implode(', ', $profile['mood_tags']);
                $parts[] = "mood tags: [{$moodTags}]";
            }

            if (!empty($parts)) {
                $flavorInfo = ' | Flavor Profile: ' . implode(', ', $parts);
            }
        }

        $priceFormatted = number_format($this->price, 0, ',', '.');
        $availability = $this->is_available && $this->stock_quantity > 0
            ? 'Available'
            : 'Not Available';

        return "[{$this->category}] {$this->name} - Rp {$priceFormatted} ({$availability}){$flavorInfo}";
    }

    /**
     * Generate detailed context untuk AI (dengan deskripsi)
     * 
     * @return string
     */
    public function toAiContextDetailed(): string
    {
        $baseContext = $this->toAiContext();

        if (!empty($this->description)) {
            $baseContext .= " | Description: {$this->description}";
        }

        return $baseContext;
    }

    /**
     * Check apakah menu bisa dipesan
     */
    public function canBeOrdered(): bool
    {
        return $this->is_available && $this->stock_quantity > 0;
    }

    /**
     * Kurangi stock saat order
     */
    public function decrementStock(int $quantity = 1): bool
    {
        if ($this->stock_quantity < $quantity) {
            return false;
        }

        $this->decrement('stock_quantity', $quantity);
        return true;
    }

    /**
     * Get all available categories
     */
    public static function getCategories(): array
    {
        return [
            self::CATEGORY_COFFEE,
            self::CATEGORY_NON_COFFEE,
            self::CATEGORY_PASTRY,
            self::CATEGORY_MAIN_COURSE,
        ];
    }
}
