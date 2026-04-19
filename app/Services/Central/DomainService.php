<?php

namespace App\Services\Central;

use App\Models\Central\Tenant;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\Collection;
use Stancl\Tenancy\Database\Models\Domain;

/**
 * Class DomainService
 * * Handles business logic related to tenant domains.
 */
class DomainService
{
    /**
     * Retrieve all domains for a specific tenant.
     *
     * @param Tenant $tenant
     * @return Collection
     */
    public function getDomainsForTenant(Tenant $tenant): Collection
    {
        return $tenant->domains;
    }

    /**
     * Attach a new custom domain to a tenant.
     *
     * @param Tenant $tenant
     * @param array $data Validated domain data.
     * @return Domain
     * @throws AuthorizationException
     */
    public function storeDomain(Tenant $tenant, array $data): Domain
    {
        if ($tenant->plan && ! $tenant->plan->allows_custom_domain) {
            throw new AuthorizationException('Your current plan does not allow custom domains.');
        }

        return $tenant->domains()->create([
            'domain' => strtolower($data['domain'])
        ]);
    }

    /**
     * Remove a domain from a tenant.
     *
     * @param Tenant $tenant
     * @param string $domain
     * @return void
     */
    public function deleteDomain(Tenant $tenant, string $domain): void
    {
        $tenant->domains()->where('domain', $domain)->delete();
    }
}
