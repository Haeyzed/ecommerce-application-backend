<?php

namespace App\Http\Controllers\Central\Api;

use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\Central\Tenant\StoreTenantRequest;
use App\Http\Requests\Central\Tenant\UpdateTenantRequest;
use App\Models\Central\Tenant;
use App\Services\Central\TenantService;
use Illuminate\Http\JsonResponse;

/**
 * Tenant Endpoints
 * * Handles the provisioning, updating, and deletion of e-commerce stores (tenants).
 */
class TenantController extends Controller
{
    /**
     * Create a new TenantController instance.
     *
     * @param TenantService $tenantService
     */
    public function __construct(
        private readonly TenantService $tenantService
    ) {}

    /**
     * List all tenants.
     * * Retrieves a paginated list of all provisioned stores.
     *
     * @return JsonResponse
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
     * Provision a new tenant.
     * * Creates a new isolated store database and assigns the default subdomain.
     *
     * @param StoreTenantRequest $request
     * @return JsonResponse
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
     * Get tenant details.
     * * Retrieves the details, domains, and plan of a specific tenant.
     *
     * @param Tenant $tenant
     * @return JsonResponse
     */
    public function show(Tenant $tenant): JsonResponse
    {
        $tenantDetails = $this->tenantService->getTenantDetails($tenant);

        return ApiResponse::success(
            ['tenant' => $tenantDetails],
            'Tenant retrieved successfully'
        );
    }

    /**
     * Update tenant details.
     * * Modifies the tenant's basic information or subscription plan.
     *
     * @param UpdateTenantRequest $request
     * @param Tenant $tenant
     * @return JsonResponse
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
     *
     * @param Tenant $tenant
     * @return JsonResponse
     */
    public function destroy(Tenant $tenant): JsonResponse
    {
        $this->tenantService->deleteTenant($tenant);

        return ApiResponse::success(null, 'Tenant deleted successfully');
    }
}
