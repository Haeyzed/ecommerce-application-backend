<?php

namespace App\Models\Tenant;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * Class Payment
 *
 * Represents a payment transaction for an order.
 *
 * @property int $id The unique identifier of the payment.
 * @property int $order_id The foreign key referencing the associated order.
 * @property string $provider The payment gateway or provider used.
 * @property string|null $provider_ref The reference ID from the payment provider.
 * @property string $amount The payment amount.
 * @property string $currency The ISO currency code for the payment.
 * @property string $status The current status of the payment.
 * @property Carbon|null $paid_at Timestamp of when the payment was successfully processed.
 * @property array|null $meta Additional metadata from the payment provider.
 * @property Carbon|null $created_at Timestamp of when the payment record was created.
 * @property Carbon|null $updated_at Timestamp of when the payment record was last updated.
 *
 * @property-read Order $order The order this payment is for.
 *
 * @package App\Models\Tenant
 */
class Payment extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'order_id',
        'provider',
        'provider_ref',
        'amount',
        'currency',
        'status',
        'paid_at',
        'meta',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'paid_at' => 'datetime',
            'meta' => 'array',
        ];
    }

    /**
     * Get the order that the payment is for.
     *
     * @return BelongsTo
     */
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }
}
