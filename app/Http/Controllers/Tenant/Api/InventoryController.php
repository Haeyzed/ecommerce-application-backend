<?php

namespace App\Http\Controllers\Tenant\Api;

use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\Tenant\Inventory\AdjustInventoryRequest;
use App\Models\Tenant\Product;
use App\Services\Tenant\ProductService;
use Illuminate\Http\JsonResponse;
use Throwable;

/**
 * Inventory Endpoints
 * * Handles stock adjustments and movement tracking for products.
 */
class InventoryController extends Controller
{
    /**
     * Create a new InventoryController instance.
     */
    public function __construct(
        private readonly ProductService $productService
    ) {
        $this->middleware('permission:manage product inventory')->only(['adjust']);
        $this->middleware('permission:view inventory movements')->only(['movements']);
    }

    /**
     * Adjust product stock levels manually.
     *
     * @throws Throwable
     */
    public function adjust(AdjustInventoryRequest $request, Product $product): JsonResponse
    {
        $movement = $this->productService->adjustStock(
            $product,
            (int) $request->validated('qty_change'),
            $request->validated('reason'),
            $request->validated('note')
        );

        return ApiResponse::success(
            ['movement' => $movement],
            'Inventory adjusted successfully'
        );
    }

    /**
     * Retrieve inventory movements for a product.
     */
    public function movements(Product $product): JsonResponse
    {
        $product->load(['inventoryMovements' => fn ($q) => $q->latest()->limit(100)]);

        return ApiResponse::success(
            ['product' => $product],
            'Inventory movements retrieved successfully'
        );
    }
}
