<?php

namespace App\Models\Tenant;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * Class CartItem
 *
 * Represents an individual product line item within a shopping cart.
 *
 * @property int $id The unique identifier of the cart item.
 * @property int $cart_id The foreign key referencing the cart.
 * @property int $product_id The foreign key referencing the product.
 * @property int $qty The quantity of the product in the cart.
 * @property string $unit_price The unit price of the product when added to the cart.
 * @property int|null $original_price Original price before discount (in cents)
 * @property int $discount_amount Discount per unit (in cents)
 * @property Carbon|null $created_at Timestamp of when the cart item was created.
 * @property Carbon|null $updated_at Timestamp of when the cart item was last updated.
 * @property-read Cart $cart The cart this item belongs to.
 * @property-read Product $product The product added to the cart.
 */
class CartItem extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'cart_id',
        'product_id',
        'qty',
        'unit_price',
        'original_price',
        'discount_amount',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'unit_price' => 'decimal:2',
            'original_price' => 'integer',
            'discount_amount' => 'integer',
            'qty' => 'integer',
        ];
    }

    /**
     * Get the cart that the item belongs to.
     */
    public function cart(): BelongsTo
    {
        return $this->belongsTo(Cart::class);
    }

    /**
     * Get the product associated with the cart item.
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Subtotal before discount (in cents)
     */
    public function getSubtotalAttribute(): int
    {
        return $this->qty * $this->unit_price;
    }

    /**
     * Total after discount (in cents)
     */
    public function getTotalAttribute(): int
    {
        return ($this->qty * $this->unit_price) - ($this->qty * $this->discount_amount);
    }
}
