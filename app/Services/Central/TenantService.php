<?php

namespace App\Services\Central;

use App\Models\Central\Tenant;
use App\Models\Central\User;
use App\Notifications\Central\DynamicTemplateNotification;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection as SupportCollection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
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
            $subdomain = Str::slug($data['subdomain']);
            $centralDomain = config('tenancy.central_domains')[0] ?? 'localhost';
            $tenantDomain = "{$subdomain}.{$centralDomain}";
            $rawPassword = Str::password(8);

            $tenant = Tenant::query()->create([
                'id' => $subdomain,
                'name' => $data['name'],
                'owner_email' => $data['owner_email'],
                'plan_id' => $data['plan_id'] ?? null,
            ]);

            $tenant->domains()->create([
                'domain' => $tenantDomain,
            ]);

            // Notify the owner user of the new tenant, if the user exists
            if (! empty($data['owner_email'])) {
                $user = User::query()->where('email', $data['owner_email'])->first();
                if ($user) {
                    $user->notify(new DynamicTemplateNotification(
                        event: 'tenant_registered',
                        templateData: [
                            'tenant_name' => $tenant->name,
                            'domain' => $tenantDomain,
                            'email' => $data['owner_email'],
                            'password' => $rawPassword,
                        ]
                    ));
                } else {
                    // Send to an anonymous notifiable via the email address
                    Notification::route('mail', $data['owner_email'])
                        ->notify(new DynamicTemplateNotification(
                            event: 'tenant_registered',
                            templateData: [
                                'tenant_name' => $tenant->name,
                                'domain' => $tenantDomain,
                                'email' => $data['owner_email'],
                                'password' => $rawPassword,
                            ]
                        ));
                }
            }

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

    /**
     * Retrieve all tenants as dropdown options.
     *
     * @return SupportCollection<int, array{value: string, label: string}>
     */
    public function getDropdownOptions(): SupportCollection
    {
        return Tenant::query()
            ->orderBy('id')
            ->get(['id', 'name'])
            ->map(fn (Tenant $tenant): array => [
                'value' => $tenant->id,
                'label' => $tenant->name ?? $tenant->id,
            ]);
    }
}
