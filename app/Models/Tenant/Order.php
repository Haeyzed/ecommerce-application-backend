<?php

namespace App\Models\Tenant;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

/**
 * Class Order
 *
 * Represents an order placed in the tenant's store.
 *
 * @property int $id The unique identifier of the order.
 * @property string $number The unique, human-readable order number (e.g., ORD-XYZ).
 * @property int $customer_id The foreign key referencing the customer.
 * @property string $status The current status of the order (e.g., pending, paid, cancelled, shipped).
 * @property string $subtotal The subtotal of the order before discounts and tax.
 * @property string $discount The total discount applied to the order.
 * @property string $tax The calculated tax for the order.
 * @property string $total The final total order amount.
 * @property string $currency The ISO currency code for the order.
 * @property array|null $shipping_address The JSON structure containing the shipping details.
 * @property array|null $billing_address The JSON structure containing the billing details.
 * @property string|null $notes Optional notes or instructions provided by the customer.
 * @property string|null $cancellation_reason The reason provided if the order was cancelled.
 * @property Carbon|null $paid_at Timestamp of when the order was paid.
 * @property Carbon|null $cancelled_at Timestamp of when the order was cancelled.
 * @property Carbon|null $created_at Timestamp of when the order was placed.
 * @property Carbon|null $updated_at Timestamp of when the order was last updated.
 * @property-read Collection|OrderItem[] $items The line items belonging to this order.
 * @property-read Customer $customer The customer who placed the order.
 */
class Order extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'number',
        'customer_id',
        'status',
        'subtotal',
        'discount',
        'tax',
        'total',
        'total_cent',
        'currency',
        'shipping_address',
        'billing_address',
        'notes',
        'cancellation_reason',
        'paid_at',
        'cancelled_at',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'subtotal' => 'decimal:2',
            'discount' => 'decimal:2',
            'tax' => 'decimal:2',
            'total' => 'decimal:2',
            'shipping_address' => 'array',
            'billing_address' => 'array',
            'paid_at' => 'datetime',
            'cancelled_at' => 'datetime',
        ];
    }

    /**
     * Get the items that belong to the order.
     */
    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    /**
     * Get the customer that placed the order.
     */
    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }
}
