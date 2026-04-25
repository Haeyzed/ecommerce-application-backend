<?php

namespace App\Http\Controllers\Central\Api;

use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\Central\Domain\StoreDomainRequest;
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
     *
     * @param DomainService $domainService
     */
    public function __construct(
        private readonly DomainService $domainService
    ) {}

    /**
     * List tenant domains.
     * Retrieves all domains attached to a specific tenant.
     *
     * @param Tenant $tenant
     * @return JsonResponse
     */
    public function index(Tenant $tenant): JsonResponse
    {
        $domains = $this->domainService->getDomainsForTenant($tenant);

        return ApiResponse::success(
            ['domains' => $domains],
            'Domains retrieved successfully'
        );
    }

    /**
     * Add a custom domain.
     * Attaches a new custom domain to the tenant if their plan permits it.
     *
     * @param StoreDomainRequest $request
     * @param Tenant $tenant
     * @return JsonResponse
     */
    public function store(StoreDomainRequest $request, Tenant $tenant): JsonResponse
    {
        try {
            $domain = $this->domainService->storeDomain($tenant, $request->validated());

            return ApiResponse::success(
                ['domain' => $domain],
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
     *
     * @param Tenant $tenant
     * @param string $domain
     * @return JsonResponse
     */
    public function destroy(Tenant $tenant, string $domain): JsonResponse
    {
        $this->domainService->deleteDomain($tenant, $domain);

        return ApiResponse::success(null, 'Domain deleted successfully');
    }
}
