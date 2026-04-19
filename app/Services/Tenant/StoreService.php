<?php

namespace App\Services\Tenant;

/**
 * Class StoreService
 * * Handles business logic related to tenant storefront metadata.
 */
class StoreService
{
    /**
     * Retrieve metadata about the current tenant.
     *
     * @return array
     */
    public function getCurrentTenantDetails(): array
    {
        $tenant = tenant();

        return [
            'id'      => $tenant->id,
            'name'    => $tenant->name,
            'domains' => $tenant->domains->pluck('domain'),
        ];
    }
}
