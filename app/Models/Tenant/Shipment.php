<?php

namespace App\Models\Tenant;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * Class Shipment
 *
 * Represents a delivery shipment associated with an order.
 *
 * @property int $id The unique identifier of the shipment.
 * @property int $order_id The foreign key referencing the associated order.
 * @property string $carrier The shipping carrier used.
 * @property string|null $tracking_number The tracking number for the shipment.
 * @property string $status The current status of the shipment.
 * @property Carbon|null $shipped_at Timestamp of when the shipment was sent.
 * @property Carbon|null $delivered_at Timestamp of when the shipment was delivered.
 * @property Carbon|null $created_at Timestamp of when the shipment record was created.
 * @property Carbon|null $updated_at Timestamp of when the shipment record was last updated.
 *
 * @property-read Order $order The order this shipment belongs to.
 *
 * @package App\Models\Tenant
 */
class Shipment extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'order_id',
        'carrier',
        'tracking_number',
        'status',
        'shipped_at',
        'delivered_at',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'shipped_at' => 'datetime',
            'delivered_at' => 'datetime',
        ];
    }

    /**
     * Get the order that the shipment belongs to.
     *
     * @return BelongsTo
     */
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }
}
