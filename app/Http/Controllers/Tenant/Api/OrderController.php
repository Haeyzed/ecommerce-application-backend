<?php

namespace App\Http\Controllers\Tenant\Api;

use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\Tenant\Order\StoreOrderRequest;
use App\Services\Tenant\OrderService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Throwable;

/**
 * Order Endpoints
 * * Handles the creation and retrieval of customer orders.
 */
class OrderController extends Controller
{
    /**
     * Create a new OrderController instance.
     *
     * @param OrderService $orderService
     */
    public function __construct(
        private readonly OrderService $orderService
    ) {}

    /**
     * List customer orders.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $orders = $this->orderService->getPaginatedCustomerOrders($request->user()->id);

        return ApiResponse::success(
            ['orders' => $orders],
            'Orders retrieved successfully'
        );
    }

    /**
     * Get order details.
     *
     * @param Request $request
     * @param int $id
     * @return JsonResponse
     */
    public function show(Request $request, int $id): JsonResponse
    {
        $order = $this->orderService->getCustomerOrderById($request->user()->id, $id);

        return ApiResponse::success(
            ['order' => $order],
            'Order retrieved successfully'
        );
    }

    /**
     * Submit a new order.
     *
     * @param StoreOrderRequest $request
     * @return JsonResponse
     * @throws Throwable
     */
    public function store(StoreOrderRequest $request): JsonResponse
    {
        $order = $this->orderService->createOrder(
            $request->user()->id,
            $request->validated()
        );

        return ApiResponse::success(
            ['order' => $order],
            'Order created successfully',
            null,
            201
        );
    }
}
