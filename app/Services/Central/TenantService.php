<?php

namespace App\Services\Central;

use App\Models\Central\Tenant;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Str;

/**
 * Class TenantService
 * * Handles business logic related to tenant provisioning and management.
 */
class TenantService
{
    /**
     * Retrieve a paginated list of all tenants.
     *
     * @return LengthAwarePaginator
     */
    public function getPaginatedTenants(): LengthAwarePaginator
    {
        return Tenant::with('domains')->latest()->paginate(20);
    }

    /**
     * Provision a new tenant and assign a default subdomain.
     *
     * @param array $data Validated tenant data.
     * @return Tenant
     */
    public function createTenant(array $data): Tenant
    {
        $tenant = Tenant::query()->create([
            'id'          => Str::slug($data['subdomain']),
            'name'        => $data['name'],
            'owner_email' => $data['owner_email'],
            'plan_id'     => $data['plan_id'] ?? null,
        ]);

        $centralDomain = config('tenancy.central_domains')[0] ?? 'shop.test';

        $tenant->domains()->create([
            'domain' => $data['subdomain'] . '.' . $centralDomain,
        ]);

        return $tenant->load('domains');
    }

    /**
     * Retrieve a specific tenant with relationships.
     *
     * @param Tenant $tenant
     * @return Tenant
     */
    public function getTenantDetails(Tenant $tenant): Tenant
    {
        return $tenant->load(['domains', 'plan']);
    }

    /**
     * Update an existing tenant.
     *
     * @param Tenant $tenant
     * @param array $data Validated update data.
     * @return Tenant
     */
    public function updateTenant(Tenant $tenant, array $data): Tenant
    {
        $tenant->update($data);

        return $tenant->fresh(['domains', 'plan']);
    }

    /**
     * Delete a tenant.
     *
     * @param Tenant $tenant
     * @return void
     */
    public function deleteTenant(Tenant $tenant): void
    {
        $tenant->delete();
    }
}
