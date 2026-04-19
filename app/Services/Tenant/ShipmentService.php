<?php

namespace App\Services\Tenant;

use App\Models\Tenant\Order;
use App\Models\Tenant\Shipment;
use Illuminate\Support\Facades\DB;
use Throwable;

/**
 * Class ShipmentService
 * * Handles business logic related to tenant order shipments.
 */
class ShipmentService
{
    /**
     * Create a shipment for an order.
     *
     * @param Order $order
     * @param array $data Validated shipment data.
     * @return Shipment
     * @throws Throwable
     */
    public function shipOrder(Order $order, array $data): Shipment
    {
        return DB::transaction(function () use ($order, $data) {
            $shipment = Shipment::query()->create([
                'order_id'        => $order->id,
                'carrier'         => $data['carrier'] ?? null,
                'tracking_number' => $data['tracking_number'] ?? null,
                'status'          => 'shipped',
                'shipped_at'      => now(),
            ]);

            $order->update(['status' => 'shipped']);

            return $shipment;
        });
    }

    /**
     * Mark a shipment as delivered.
     *
     * @param Shipment $shipment
     * @return Shipment
     * @throws Throwable
     */
    public function markShipmentDelivered(Shipment $shipment): Shipment
    {
        return DB::transaction(function () use ($shipment) {
            $shipment->update([
                'status'       => 'delivered',
                'delivered_at' => now(),
            ]);

            $shipment->order->update(['status' => 'completed']);

            return $shipment->fresh();
        });
    }
}
