<?php

namespace App\Models\Tenant;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * Class InventoryMovement
 *
 * Tracks historical changes to a product's stock levels.
 *
 * @property int $id The unique identifier of the inventory movement.
 * @property int $product_id The foreign key referencing the product.
 * @property int $qty_change The quantity changed (positive or negative).
 * @property string $reason The reason for the movement (e.g., sale, restock).
 * @property string|null $reference_type The type of the reference entity (e.g., Order).
 * @property int|null $reference_id The ID of the reference entity.
 * @property string|null $note Additional notes about the movement.
 * @property Carbon|null $created_at Timestamp of when the movement was created.
 * @property Carbon|null $updated_at Timestamp of when the movement was last updated.
 *
 * @property-read Product $product The product associated with this movement.
 *
 * @package App\Models\Tenant
 */
class InventoryMovement extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'product_id',
        'qty_change',
        'reason',
        'reference_type',
        'reference_id',
        'note',
    ];

    /**
     * Get the product associated with the inventory movement.
     *
     * @return BelongsTo
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
