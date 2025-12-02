<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * CustomerInteraction Model - Untuk Empathy Radar feature
 * 
 * @property int $id
 * @property int|null $order_id
 * @property string $interaction_type complaint, feedback, praise, question
 * @property string $channel in-store, online, phone
 * @property string $customer_message
 * @property string|null $sentiment_type positive, neutral, negative, etc.
 * @property float $sentiment_score 0.0 to 1.0
 * @property string|null $ai_analysis AI analysis result
 * @property string|null $suggested_response AI suggested response
 * @property string|null $staff_notes
 * @property int|null $handled_by User ID who handled
 * @property \DateTime|null $resolved_at
 * @property \DateTime $created_at
 * @property \DateTime $updated_at
 */
class CustomerInteraction extends Model
{
    use HasFactory;

    /**
     * Interaction type constants
     */
    public const TYPE_COMPLAINT = 'complaint';
    public const TYPE_FEEDBACK = 'feedback';
    public const TYPE_PRAISE = 'praise';
    public const TYPE_QUESTION = 'question';

    /**
     * Channel constants
     */
    public const CHANNEL_IN_STORE = 'in-store';
    public const CHANNEL_ONLINE = 'online';
    public const CHANNEL_PHONE = 'phone';

    /**
     * Sentiment type constants
     */
    public const SENTIMENT_POSITIVE = 'positive';
    public const SENTIMENT_NEUTRAL = 'neutral';
    public const SENTIMENT_NEGATIVE = 'negative';
    public const SENTIMENT_HAPPY = 'happy';
    public const SENTIMENT_SATISFIED = 'satisfied';
    public const SENTIMENT_ANGRY = 'angry';
    public const SENTIMENT_FRUSTRATED = 'frustrated';
    public const SENTIMENT_SAD = 'sad';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'order_id',
        'interaction_type',
        'channel',
        'customer_message',
        'sentiment_type',
        'sentiment_score',
        'ai_analysis',
        'suggested_response',
        'staff_notes',
        'handled_by',
        'resolved_at',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'sentiment_score' => 'float',
            'resolved_at' => 'datetime',
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    /**
     * Order terkait (jika ada)
     */
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * Staff yang menangani interaksi ini
     */
    public function handledBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'handled_by');
    }

    /*
    |--------------------------------------------------------------------------
    | Scopes
    |--------------------------------------------------------------------------
    */

    /**
     * Scope untuk interaksi yang belum ditangani
     */
    public function scopeUnhandled($query)
    {
        return $query->whereNull('handled_by');
    }

    /**
     * Scope untuk interaksi yang sudah resolved
     */
    public function scopeResolved($query)
    {
        return $query->whereNotNull('resolved_at');
    }

    /**
     * Scope untuk sentimen negatif (perlu perhatian)
     */
    public function scopeNeedsAttention($query)
    {
        return $query->whereIn('sentiment_type', [
            self::SENTIMENT_NEGATIVE,
            self::SENTIMENT_ANGRY,
            self::SENTIMENT_FRUSTRATED,
        ])->whereNull('handled_by');
    }

    /**
     * Scope berdasarkan tipe interaksi
     */
    public function scopeOfType($query, string $type)
    {
        return $query->where('interaction_type', $type);
    }

    /*
    |--------------------------------------------------------------------------
    | Helper Methods
    |--------------------------------------------------------------------------
    */

    /**
     * Cek apakah interaksi ini sudah ditangani
     */
    public function isHandled(): bool
    {
        return $this->handled_by !== null;
    }

    /**
     * Cek apakah interaksi ini sudah resolved
     */
    public function isResolved(): bool
    {
        return $this->resolved_at !== null;
    }

    /**
     * Cek apakah sentimen negatif
     */
    public function isNegativeSentiment(): bool
    {
        return in_array($this->sentiment_type, [
            self::SENTIMENT_NEGATIVE,
            self::SENTIMENT_ANGRY,
            self::SENTIMENT_FRUSTRATED,
            self::SENTIMENT_SAD,
        ]);
    }

    /**
     * Get sentiment emoji
     */
    public function getSentimentEmoji(): string
    {
        return match($this->sentiment_type) {
            self::SENTIMENT_POSITIVE, self::SENTIMENT_HAPPY, self::SENTIMENT_SATISFIED => '😊',
            self::SENTIMENT_ANGRY => '😠',
            self::SENTIMENT_FRUSTRATED => '😤',
            self::SENTIMENT_SAD => '😢',
            self::SENTIMENT_NEGATIVE => '😔',
            default => '😐',
        };
    }
}
