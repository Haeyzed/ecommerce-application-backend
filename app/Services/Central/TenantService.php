<?php

namespace App\Services\Central;

use App\Models\Central\Tenant;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Throwable;

/**
 * Class TenantService
 * * Handles central business logic related to tenant provisioning and management.
 */
class TenantService
{
    /**
     * Retrieve a paginated list of tenants.
     */
    public function getPaginatedTenants(int $perPage = 20): LengthAwarePaginator
    {
        return Tenant::query()
            ->with('domains')
            ->latest()
            ->paginate($perPage);
    }

    /**
     * Provision a new tenant and assign a default subdomain.
     *
     * @param  array  $data  Validated tenant data.
     *
     * @throws Throwable
     */
    public function createTenant(array $data): Tenant
    {
        return DB::transaction(function () use ($data) {
            $tenant = Tenant::query()->create([
                'id' => Str::slug($data['subdomain']),
                'name' => $data['name'],
                'owner_email' => $data['owner_email'],
                'plan_id' => $data['plan_id'] ?? null,
            ]);

            $centralDomain = config('tenancy.central_domains')[0] ?? 'localhost';

            $tenant->domains()->create([
                'domain' => $data['subdomain'].'.'.$centralDomain,
            ]);

            return $tenant->load('domains');
        });
    }

    /**
     * Retrieve a specific tenant by ID.
     */
    public function getTenantById(string $id): Tenant
    {
        return Tenant::query()
            ->with(['domains', 'plan'])
            ->findOrFail($id);
    }

    /**
     * Update an existing tenant.
     *
     * @param  array  $data  Validated update data.
     */
    public function updateTenant(Tenant $tenant, array $data): Tenant
    {
        $tenant->update($data);

        return $tenant->fresh(['domains', 'plan']);
    }

    /**
     * Delete a tenant.
     */
    public function deleteTenant(Tenant $tenant): void
    {
        $tenant->delete();
    }
}
