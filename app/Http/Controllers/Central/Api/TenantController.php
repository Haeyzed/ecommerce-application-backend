<?php

namespace App\Http\Controllers\Central\Api;

use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\Central\Tenant\StoreTenantRequest;
use App\Http\Requests\Central\Tenant\UpdateTenantRequest;
use App\Models\Central\Tenant;
use App\Services\Central\TenantService;
use Illuminate\Http\JsonResponse;
use Throwable;

/**
 * Tenant Endpoints
 * Handles the CRUD operations for central system tenants.
 */
class TenantController extends Controller
{
    /**
     * Create a new TenantController instance.
     */
    public function __construct(
        private readonly TenantService $tenantService
    ) {}

    /**
     * List all tenants.
     */
    public function index(): JsonResponse
    {
        $tenants = $this->tenantService->getPaginatedTenants();

        return ApiResponse::success(
            ['tenants' => $tenants],
            'Tenants retrieved successfully'
        );
    }

    /**
     * Provision a new tenant manually.
     *
     * @throws Throwable
     */
    public function store(StoreTenantRequest $request): JsonResponse
    {
        $tenant = $this->tenantService->createTenant($request->validated());

        return ApiResponse::success(
            ['tenant' => $tenant],
            'Tenant provisioned successfully',
            null,
            201
        );
    }

    /**
     * Get specific tenant details.
     */
    public function show(Tenant $tenant): JsonResponse
    {
        $tenant = $this->tenantService->getTenantById($tenant->getKey());

        return ApiResponse::success(
            ['tenant' => $tenant],
            'Tenant retrieved successfully'
        );
    }

    /**
     * Update an existing tenant.
     */
    public function update(UpdateTenantRequest $request, Tenant $tenant): JsonResponse
    {
        $updatedTenant = $this->tenantService->updateTenant($tenant, $request->validated());

        return ApiResponse::success(
            ['tenant' => $updatedTenant],
            'Tenant updated successfully'
        );
    }

    /**
     * Delete tenant.
     * * Removes the tenant and their associated domains.
     */
    public function destroy(Tenant $tenant): JsonResponse
    {
        $this->tenantService->deleteTenant($tenant);

        return ApiResponse::success(null, 'Tenant deleted successfully');
    }

    /**
     * List tenants as dropdown options.
     *
     * Returns all tenants with value (id) and label (name).
     */
    public function dropdown(): JsonResponse
    {
        $options = $this->tenantService->getDropdownOptions();

        return ApiResponse::success($options, 'Tenant dropdown options retrieved successfully');
    }
}
