<?php

namespace App\Models\Central;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasOneThrough;
use Illuminate\Support\Carbon;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;
use Stancl\Tenancy\Contracts\TenantWithDatabase;
use Stancl\Tenancy\Database\Concerns\HasDatabase;
use Stancl\Tenancy\Database\Concerns\HasDomains;
use Stancl\Tenancy\Database\Models\Domain;
use Stancl\Tenancy\Database\Models\Tenant as BaseTenant;

/**
 * Class Tenant
 *
 * Represents a tenant (store) in the central SaaS application.
 *
 * @property string $id The unique identifier/slug of the tenant.
 * @property string $name The display name of the tenant store.
 * @property string|null $owner_email The email address of the store owner.
 * @property int|null $plan_id The current subscription plan ID.
 * @property string|null $status The current status of the tenant (e.g., active, suspended).
 * @property array|null $data Additional JSON data stored by the tenancy package.
 * @property Carbon|null $created_at Timestamp of when the tenant was created.
 * @property Carbon|null $updated_at Timestamp of when the tenant was last updated.
 * @property-read Plan|null $plan The subscription plan the tenant is on.
 * @property-read Subscription|null $subscription The active subscription for the tenant.
 * @property-read Collection|Invoice[] $invoices The invoices billed to the tenant.
 * @property-read Collection|Domain[] $domains The domains associated with the tenant.
 */
class Tenant extends BaseTenant implements AuditableContract, TenantWithDatabase
{
    use Auditable, HasDatabase, HasDomains;

    /**
     * Custom columns persisted directly on the tenants table (alongside `data`).
     * Migration must add these columns.
     *
     * @return array<int, string>
     */
    public static function getCustomColumns(): array
    {
        return [
            'id',
            'name',
            'owner_email',
            'plan_id',
            'status',
        ];
    }

    /**
     * Get the plan associated with the tenant through their subscription.
     */
    public function plan(): HasOneThrough
    {
        return $this->hasOneThrough(
            Plan::class,
            Subscription::class,
            'tenant_id', // Foreign key on subscriptions table
            'id',        // Foreign key on plans table
            'id',        // Local key on tenants table
            'plan_id'    // Local key on subscriptions table
        );
    }

    /**
     * Get the active subscription for the tenant.
     */
    public function subscription(): HasOne
    {
        return $this->hasOne(Subscription::class);
    }

    /**
     * Get the invoices billed to the tenant.
     */
    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class);
    }
}
