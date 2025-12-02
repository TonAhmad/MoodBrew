<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * OrderItem Model - Detail item dalam setiap pesanan
 * 
 * @property int $id
 * @property int $order_id
 * @property int $menu_item_id
 * @property int $quantity
 * @property float $price_at_moment
 * @property string|null $note
 * @property \DateTime $created_at
 * @property \DateTime $updated_at
 */
class OrderItem extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'order_id',
        'menu_item_id',
        'quantity',
        'price_at_moment',
        'note',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'quantity' => 'integer',
            'price_at_moment' => 'decimal:2',
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    /**
     * Order yang memiliki item ini
     */
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * Menu item yang dipesan
     */
    public function menuItem(): BelongsTo
    {
        return $this->belongsTo(MenuItem::class);
    }

    /*
    |--------------------------------------------------------------------------
    | Helper Methods
    |--------------------------------------------------------------------------
    */

    /**
     * Get subtotal untuk item ini
     */
    public function getSubtotal(): float
    {
        return $this->price_at_moment * $this->quantity;
    }

    /**
     * Get formatted subtotal
     */
    public function getFormattedSubtotal(): string
    {
        return 'Rp ' . number_format($this->getSubtotal(), 0, ',', '.');
    }

    /**
     * Create order item dari menu item
     * Automatically snapshot price_at_moment
     */
    public static function createFromMenuItem(
        Order $order,
        MenuItem $menuItem,
        int $quantity = 1,
        ?string $note = null
    ): self {
        return self::create([
            'order_id' => $order->id,
            'menu_item_id' => $menuItem->id,
            'quantity' => $quantity,
            'price_at_moment' => $menuItem->price,
            'note' => $note,
        ]);
    }
}
