<?php

namespace App\Http\Controllers\Central\Api;

use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\Central\Domain\StoreDomainRequest;
use App\Http\Resources\Central\DomainResource;
use App\Models\Central\Tenant;
use App\Services\Central\DomainService;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\JsonResponse;

/**
 * Domain Endpoints
 * Handles custom and default domain management for tenants.
 */
class DomainController extends Controller
{
    /**
     * Create a new DomainController instance.
     */
    public function __construct(
        private readonly DomainService $domainService
    ) {
        $this->middleware('permission:manage tenant domains')->only(['index', 'store', 'destroy']);
    }

    /**
     * List tenant domains.
     * Retrieves all domains attached to a specific tenant.
     */
    public function index(Tenant $tenant): JsonResponse
    {
        $domains = $this->domainService->getDomainsForTenant($tenant);

        return ApiResponse::success(
            data: new DomainResource($domains),
            message: 'Domains retrieved successfully'
        );
    }

    /**
     * Add a custom domain.
     * Attaches a new custom domain to the tenant if their plan permits it.
     */
    public function store(StoreDomainRequest $request, Tenant $tenant): JsonResponse
    {
        try {
            $domain = $this->domainService->storeDomain($tenant, $request->validated());

            return ApiResponse::success(
                new DomainResource($domain),
                'Domain attached successfully',
                null,
                201
            );
        } catch (AuthorizationException $e) {
            return ApiResponse::error($e->getMessage(), null, 403);
        }
    }

    /**
     * Remove a domain.
     * Deletes a specific domain from the tenant.
     */
    public function destroy(Tenant $tenant, string $domain): JsonResponse
    {
        $this->domainService->deleteDomain($tenant, $domain);

        return ApiResponse::success(null, 'Domain deleted successfully');
    }
}
