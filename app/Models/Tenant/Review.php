<?php

namespace App\Models\Tenant;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * Class Review
 *
 * Represents a customer's review and rating for a specific product.
 *
 * @property int $id The unique identifier of the review.
 * @property int $product_id The foreign key referencing the product.
 * @property int $customer_id The foreign key referencing the customer.
 * @property int $rating The rating given by the customer.
 * @property string|null $title The title of the review.
 * @property string|null $body The detailed body of the review.
 * @property bool $is_approved Indicates whether the review is approved for display.
 * @property Carbon|null $created_at Timestamp of when the review was created.
 * @property Carbon|null $updated_at Timestamp of when the review was last updated.
 *
 * @property-read Product $product The product being reviewed.
 * @property-read Customer $customer The customer who wrote the review.
 *
 * @package App\Models\Tenant
 */
class Review extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'product_id',
        'customer_id',
        'rating',
        'title',
        'body',
        'is_approved',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'rating' => 'integer',
            'is_approved' => 'boolean',
        ];
    }

    /**
     * Get the product being reviewed.
     *
     * @return BelongsTo
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Get the customer who wrote the review.
     *
     * @return BelongsTo
     */
    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }
}
