<?php

namespace App\Models\Tenant;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * Class Wishlist
 *
 * Represents a product saved to a customer's wishlist.
 *
 * @property int $id The unique identifier of the wishlist entry.
 * @property int $customer_id The foreign key referencing the customer.
 * @property int $product_id The foreign key referencing the product.
 * @property Carbon|null $created_at Timestamp of when the wishlist entry was created.
 * @property Carbon|null $updated_at Timestamp of when the wishlist entry was last updated.
 *
 * @property-read Customer $customer The customer who owns the wishlist entry.
 * @property-read Product $product The product saved in the wishlist.
 *
 * @package App\Models\Tenant
 */
class Wishlist extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'customer_id',
        'product_id',
    ];

    /**
     * Get the customer that owns the wishlist entry.
     *
     * @return BelongsTo
     */
    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    /**
     * Get the product associated with the wishlist entry.
     *
     * @return BelongsTo
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
