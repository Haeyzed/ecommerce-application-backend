<?php

namespace App\Http\Controllers\Tenant\Api;

use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Services\Tenant\StoreService;
use Illuminate\Http\JsonResponse;

/**
 * Store Endpoints
 * * Handles public-facing metadata about the tenant's storefront.
 */
class StoreController extends Controller
{
    /**
     * Create a new StoreController instance.
     */
    public function __construct(
        private readonly StoreService $storeService
    ) {}

    /**
     * Returns metadata about the current tenant.
     */
    public function show(): JsonResponse
    {
        $details = $this->storeService->getCurrentTenantDetails();

        return ApiResponse::success(
            ['store' => $details],
            'Store details retrieved successfully'
        );
    }
}
