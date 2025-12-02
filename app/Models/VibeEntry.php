<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * VibeEntry Model - Silent Social Wall entries
 * 
 * @property int $id
 * @property string $content
 * @property float $sentiment_score
 * @property bool $is_featured
 * @property bool $is_approved
 * @property int|null $user_id
 * @property string|null $display_name
 * @property \DateTime $created_at
 * @property \DateTime $updated_at
 */
class VibeEntry extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'content',
        'sentiment_score',
        'is_featured',
        'is_approved',
        'user_id',
        'display_name',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'sentiment_score' => 'decimal:2',
            'is_featured' => 'boolean',
            'is_approved' => 'boolean',
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    /**
     * User yang membuat entry (jika login)
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /*
    |--------------------------------------------------------------------------
    | Scopes
    |--------------------------------------------------------------------------
    */

    /**
     * Scope untuk entry yang approved
     */
    public function scopeApproved($query)
    {
        return $query->where('is_approved', true);
    }

    /**
     * Scope untuk featured entries
     */
    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    /**
     * Scope untuk entry dengan sentimen positif
     */
    public function scopePositive($query, float $threshold = 0.3)
    {
        return $query->where('sentiment_score', '>=', $threshold);
    }

    /**
     * Scope untuk wall display (approved, sorted by featured then recent)
     */
    public function scopeForWall($query)
    {
        return $query->approved()
            ->orderByDesc('is_featured')
            ->orderByDesc('created_at');
    }

    /*
    |--------------------------------------------------------------------------
    | Helper Methods
    |--------------------------------------------------------------------------
    */

    /**
     * Get display name (anonymous jika tidak ada)
     */
    public function getDisplayNameAttribute(): string
    {
        return $this->attributes['display_name'] ?? 'Anonymous Brewer';
    }

    /**
     * Check apakah sentimen positif
     */
    public function isPositive(): bool
    {
        return $this->sentiment_score >= 0.3;
    }

    /**
     * Check apakah sentimen negatif
     */
    public function isNegative(): bool
    {
        return $this->sentiment_score <= -0.3;
    }

    /**
     * Check apakah sentimen netral
     */
    public function isNeutral(): bool
    {
        return $this->sentiment_score > -0.3 && $this->sentiment_score < 0.3;
    }

    /**
     * Get sentiment label
     */
    public function getSentimentLabel(): string
    {
        if ($this->isPositive()) {
            return 'positive';
        }

        if ($this->isNegative()) {
            return 'negative';
        }

        return 'neutral';
    }

    /**
     * Approve entry
     */
    public function approve(): void
    {
        $this->update(['is_approved' => true]);
    }

    /**
     * Reject/unapprove entry
     */
    public function reject(): void
    {
        $this->update(['is_approved' => false]);
    }

    /**
     * Toggle featured status
     */
    public function toggleFeatured(): void
    {
        $this->update(['is_featured' => !$this->is_featured]);
    }

    /**
     * Feature entry
     */
    public function feature(): void
    {
        $this->update(['is_featured' => true]);
    }

    /**
     * Unfeature entry
     */
    public function unfeature(): void
    {
        $this->update(['is_featured' => false]);
    }

    /**
     * Create vibe entry dengan auto sentiment (placeholder untuk AI)
     */
    public static function createEntry(
        string $content,
        ?string $displayName = null,
        ?int $userId = null,
        float $sentimentScore = 0
    ): self {
        return self::create([
            'content' => $content,
            'display_name' => $displayName,
            'user_id' => $userId,
            'sentiment_score' => $sentimentScore,
            'is_approved' => true, // Auto-approve untuk MVP, bisa diubah nanti
            'is_featured' => false,
        ]);
    }
}
