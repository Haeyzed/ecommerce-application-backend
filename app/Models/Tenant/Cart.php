<?php

namespace App\Models\Tenant;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * Class Cart
 *
 * Represents a shopping cart for a customer or a guest session.
 *
 * @property int $id The unique identifier of the cart.
 * @property int|null $customer_id The foreign key referencing the customer (if logged in).
 * @property string|null $session_token The session token for guest carts.
 * @property int|null $coupon_id The foreign key referencing an applied coupon.
 * @property string $currency The ISO currency code for the cart.
 * @property Carbon|null $created_at Timestamp of when the cart was created.
 * @property Carbon|null $updated_at Timestamp of when the cart was last updated.
 *
 * @property-read \Illuminate\Database\Eloquent\Collection|CartItem[] $items The items within the cart.
 * @property-read Customer|null $customer The customer who owns the cart.
 * @property-read Coupon|null $coupon The coupon applied to the cart.
 *
 * @package App\Models\Tenant
 */
class Cart extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'customer_id',
        'session_token',
        'coupon_id',
        'currency',
    ];

    /**
     * Get the items in the cart.
     *
     * @return HasMany
     */
    public function items(): HasMany
    {
        return $this->hasMany(CartItem::class);
    }

    /**
     * Get the customer that owns the cart.
     *
     * @return BelongsTo
     */
    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    /**
     * Get the coupon applied to the cart.
     *
     * @return BelongsTo
     */
    public function coupon(): BelongsTo
    {
        return $this->belongsTo(Coupon::class);
    }

    /**
     * Calculate the subtotal of the cart.
     *
     * @return float
     */
    public function subtotal(): float
    {
        return (float) $this->items->sum(fn ($i) => $i->qty * $i->unit_price);
    }
}
