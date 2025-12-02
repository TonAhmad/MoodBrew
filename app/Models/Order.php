<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Carbon\Carbon;

/**
 * Order Model - Transaksi utama dengan hybrid ordering support
 * 
 * @property int $id
 * @property string $order_number
 * @property string|null $table_number
 * @property float $total_amount
 * @property string $status
 * @property string|null $customer_mood_summary
 * @property string|null $payment_method
 * @property int|null $user_id
 * @property int|null $cashier_id
 * @property int|null $applied_flash_sale_id
 * @property \DateTime $created_at
 * @property \DateTime $updated_at
 */
class Order extends Model
{
    use HasFactory;

    /**
     * Status constants
     */
    public const STATUS_PENDING_PAYMENT = 'pending_payment';
    public const STATUS_PREPARING = 'preparing';
    public const STATUS_READY = 'ready';
    public const STATUS_COMPLETED = 'completed';
    public const STATUS_CANCELLED = 'cancelled';

    /**
     * Payment method constants
     */
    public const PAYMENT_CASH = 'cash';
    public const PAYMENT_QRIS = 'qris';
    public const PAYMENT_DEBIT = 'debit';
    public const PAYMENT_CREDIT = 'credit';
    public const PAYMENT_OTHER = 'other';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'order_number',
        'table_number',
        'customer_name',
        'total_amount',
        'status',
        'notes',
        'customer_mood_summary',
        'payment_method',
        'amount_paid',
        'change_amount',
        'user_id',
        'cashier_id',
        'applied_flash_sale_id',
        'paid_at',
        'completed_at',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'total_amount' => 'decimal:2',
            'amount_paid' => 'decimal:2',
            'change_amount' => 'decimal:2',
            'paid_at' => 'datetime',
            'completed_at' => 'datetime',
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

        // Auto-generate order number
        static::creating(function (Order $order) {
            if (empty($order->order_number)) {
                $order->order_number = self::generateOrderNumber();
            }
        });
    }

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    /**
     * Customer yang membuat order (nullable untuk guest)
     */
    public function customer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Kasir yang memproses pembayaran
     */
    public function cashier(): BelongsTo
    {
        return $this->belongsTo(User::class, 'cashier_id');
    }

    /**
     * Flash sale yang diaplikasikan
     */
    public function flashSale(): BelongsTo
    {
        return $this->belongsTo(FlashSale::class, 'applied_flash_sale_id');
    }

    /**
     * Item-item dalam order ini
     */
    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    /**
     * Alias untuk orderItems (untuk backward compatibility)
     */
    public function items(): HasMany
    {
        return $this->orderItems();
    }

    /*
    |--------------------------------------------------------------------------
    | Scopes
    |--------------------------------------------------------------------------
    */

    /**
     * Scope untuk order yang pending payment (untuk dashboard kasir)
     */
    public function scopePendingPayment($query)
    {
        return $query->where('status', self::STATUS_PENDING_PAYMENT);
    }

    /**
     * Scope untuk order yang sedang diproses
     */
    public function scopeInProgress($query)
    {
        return $query->whereIn('status', [
            self::STATUS_PENDING_PAYMENT,
            self::STATUS_PREPARING,
            self::STATUS_READY,
        ]);
    }

    /**
     * Scope untuk order hari ini
     */
    public function scopeToday($query)
    {
        return $query->whereDate('created_at', Carbon::today());
    }

    /**
     * Scope untuk filter by table
     */
    public function scopeByTable($query, string $tableNumber)
    {
        return $query->where('table_number', $tableNumber);
    }

    /*
    |--------------------------------------------------------------------------
    | Helper Methods
    |--------------------------------------------------------------------------
    */

    /**
     * Generate unique order number
     * Format: ORD-MMDD-XXX
     */
    public static function generateOrderNumber(): string
    {
        $datePrefix = Carbon::now()->format('md');
        $todayOrderCount = self::whereDate('created_at', Carbon::today())->count() + 1;

        return sprintf('ORD-%s-%03d', $datePrefix, $todayOrderCount);
    }

    /**
     * Calculate total dari order items
     */
    public function calculateTotal(): float
    {
        $subtotal = $this->orderItems->sum(function ($item) {
            return $item->price_at_moment * $item->quantity;
        });

        // Apply flash sale discount jika ada
        if ($this->flashSale && $this->flashSale->isCurrentlyValid()) {
            $subtotal = $this->flashSale->calculateFinalPrice($subtotal);
        }

        return $subtotal;
    }

    /**
     * Update total amount
     */
    public function updateTotal(): void
    {
        $this->update(['total_amount' => $this->calculateTotal()]);
    }

    /**
     * Mark order sebagai paid
     */
    public function markAsPaid(string $paymentMethod, int $cashierId): void
    {
        $this->update([
            'status' => self::STATUS_PAID_PREPARING,
            'payment_method' => $paymentMethod,
            'cashier_id' => $cashierId,
        ]);
    }

    /**
     * Mark order sebagai served
     */
    public function markAsServed(): void
    {
        $this->update(['status' => self::STATUS_SERVED]);
    }

    /**
     * Mark order sebagai completed
     */
    public function markAsCompleted(): void
    {
        $this->update(['status' => self::STATUS_COMPLETED]);
    }

    /**
     * Cancel order
     */
    public function cancel(): void
    {
        $this->update(['status' => self::STATUS_CANCELLED]);
    }

    /**
     * Check apakah order bisa dibayar
     */
    public function canBePaid(): bool
    {
        return $this->status === self::STATUS_PENDING_PAYMENT;
    }

    /**
     * Check apakah order bisa di-cancel
     */
    public function canBeCancelled(): bool
    {
        return in_array($this->status, [
            self::STATUS_PENDING_PAYMENT,
            self::STATUS_PAID_PREPARING,
        ]);
    }

    /**
     * Get status label untuk display
     */
    public function getStatusLabel(): string
    {
        return match ($this->status) {
            self::STATUS_PENDING_PAYMENT => 'Menunggu Pembayaran',
            self::STATUS_PAID_PREPARING => 'Sedang Disiapkan',
            self::STATUS_SERVED => 'Sudah Disajikan',
            self::STATUS_COMPLETED => 'Selesai',
            self::STATUS_CANCELLED => 'Dibatalkan',
            default => 'Unknown',
        };
    }

    /**
     * Get all available statuses
     */
    public static function getStatuses(): array
    {
        return [
            self::STATUS_PENDING_PAYMENT,
            self::STATUS_PAID_PREPARING,
            self::STATUS_SERVED,
            self::STATUS_COMPLETED,
            self::STATUS_CANCELLED,
        ];
    }

    /**
     * Get all payment methods
     */
    public static function getPaymentMethods(): array
    {
        return [
            self::PAYMENT_CASH,
            self::PAYMENT_QRIS,
            self::PAYMENT_DEBIT,
            self::PAYMENT_OTHER,
        ];
    }
}
