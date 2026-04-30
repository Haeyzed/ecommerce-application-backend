<?php

namespace App\Http\Controllers\Tenant\Api;

use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\Tenant\Shipment\StoreShipmentRequest;
use App\Models\Tenant\Order;
use App\Models\Tenant\Shipment;
use App\Services\Tenant\ShipmentService;
use Illuminate\Http\JsonResponse;
use Throwable;

/**
 * Shipment Endpoints
 * * Handles the creation and tracking of order shipments.
 */
class ShipmentController extends Controller
{
    /**
     * Create a new ShipmentController instance.
     */
    public function __construct(
        private readonly ShipmentService $shipmentService
    ) {}

    /**
     * Dispatch a shipment for an order.
     *
     * @throws Throwable
     */
    public function store(StoreShipmentRequest $request, Order $order): JsonResponse
    {
        $shipment = $this->shipmentService->shipOrder($order, $request->validated());

        return ApiResponse::success(
            ['shipment' => $shipment],
            'Shipment created successfully',
            null,
            201
        );
    }

    /**
     * Mark a shipment as delivered.
     *
     * @throws Throwable
     */
    public function deliver(Shipment $shipment): JsonResponse
    {
        $deliveredShipment = $this->shipmentService->markShipmentDelivered($shipment);

        return ApiResponse::success(
            ['shipment' => $deliveredShipment],
            'Shipment marked as delivered'
        );
    }
}
