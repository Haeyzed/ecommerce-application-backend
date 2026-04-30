<?php

namespace App\Models\Tenant;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * Class OrderItem
 *
 * Represents a specific line item within a customer's order.
 *
 * @property int $id The unique identifier of the order item.
 * @property int $order_id The foreign key referencing the parent order.
 * @property int|null $product_id The foreign key referencing the purchased product.
 * @property string|null $name The snapshot name of the product at the time of purchase.
 * @property string $unit_price The snapshot price of a single unit.
 * @property int $qty The quantity of the product purchased.
 * @property string $line_total The total price for this line (qty * unit_price).
 * @property Carbon|null $created_at Timestamp of when the order item was created.
 * @property Carbon|null $updated_at Timestamp of when the order item was last updated.
 * @property-read Product|null $product The original product associated with this item.
 */
class OrderItem extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'order_id',
        'product_id',
        'name',
        'unit_price',
        'qty',
        'line_total',
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
            'line_total' => 'decimal:2',
            'qty' => 'integer',
        ];
    }

    /**
     * Get the product associated with this order item.
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
