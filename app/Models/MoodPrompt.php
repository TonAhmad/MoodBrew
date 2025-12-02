<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * MoodPrompt Model - History percakapan AI untuk mood-based recommendation
 * 
 * @property int $id
 * @property string $user_input
 * @property string $ai_response
 * @property int|null $recommended_menu_id
 * @property int|null $user_id
 * @property string|null $session_id
 * @property \DateTime $created_at
 * @property \DateTime $updated_at
 */
class MoodPrompt extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'user_input',
        'ai_response',
        'recommended_menu_id',
        'user_id',
        'session_id',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    /**
     * Menu yang direkomendasikan
     */
    public function recommendedMenu(): BelongsTo
    {
        return $this->belongsTo(MenuItem::class, 'recommended_menu_id');
    }

    /**
     * User yang melakukan prompt (jika login)
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
     * Scope untuk filter by session
     */
    public function scopeBySession($query, string $sessionId)
    {
        return $query->where('session_id', $sessionId);
    }

    /**
     * Scope untuk prompt dengan recommendation
     */
    public function scopeWithRecommendation($query)
    {
        return $query->whereNotNull('recommended_menu_id');
    }

    /*
    |--------------------------------------------------------------------------
    | Helper Methods
    |--------------------------------------------------------------------------
    */

    /**
     * Create prompt record
     */
    public static function createPrompt(
        string $userInput,
        string $aiResponse,
        ?int $recommendedMenuId = null,
        ?int $userId = null,
        ?string $sessionId = null
    ): self {
        return self::create([
            'user_input' => $userInput,
            'ai_response' => $aiResponse,
            'recommended_menu_id' => $recommendedMenuId,
            'user_id' => $userId,
            'session_id' => $sessionId ?? session()->getId(),
        ]);
    }

    /**
     * Check apakah ada menu recommendation
     */
    public function hasRecommendation(): bool
    {
        return $this->recommended_menu_id !== null;
    }

    /**
     * Get conversation history untuk context AI
     */
    public static function getConversationContext(?string $sessionId = null, int $limit = 5): array
    {
        $query = self::query();

        if ($sessionId) {
            $query->where('session_id', $sessionId);
        }

        return $query->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get()
            ->map(function ($prompt) {
                return [
                    'user' => $prompt->user_input,
                    'assistant' => $prompt->ai_response,
                ];
            })
            ->reverse()
            ->values()
            ->toArray();
    }
}
