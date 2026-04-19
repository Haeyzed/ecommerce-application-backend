<?php

namespace App\Models\Central;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Stancl\Tenancy\Contracts\TenantWithDatabase;
use Stancl\Tenancy\Database\Concerns\HasDatabase;
use Stancl\Tenancy\Database\Concerns\HasDomains;
use Stancl\Tenancy\Database\Models\Domain;
use Stancl\Tenancy\Database\Models\Tenant as BaseTenant;
use Illuminate\Support\Carbon;

/**
 * Class Tenant
 *
 * Represents a tenant (e.g., an e-commerce store) within the central database.
 * @property string $id The unique identifier for the tenant (often used as the default subdomain).
 * @property string $name The name of the tenant's store or business.
 * @property int|null $plan_id The foreign key referencing the subscription plan.
 * @property string $owner_email The email address of the store owner.
 * @property Carbon|null $created_at Timestamp of when the tenant was created.
 * @property Carbon|null $updated_at Timestamp of when the tenant was last updated.
 * @property-read Plan|null $plan The subscription plan the tenant is subscribed to.
 * @property-read Collection|Domain[] $domains The custom and default domains associated with the tenant.
 *
 * @package App\Models\Central
 */
class Tenant extends BaseTenant implements TenantWithDatabase
{
    use HasDatabase, HasDomains;

    /**
     * Define the custom columns for the tenant model.
     * * By default, stancl/tenancy stores extra data in a JSON column.
     * Specifying columns here tells the package to map these attributes
     * directly to physical database columns instead.
     *
     * @return array<int, string>
     */
    public static function getCustomColumns(): array
    {
        return [
            'id',
            'name',
            'plan_id',
            'owner_email',
            'created_at',
            'updated_at'
        ];
    }

    /**
     * Get the subscription plan associated with the tenant.
     *
     * @return BelongsTo
     */
    public function plan(): BelongsTo
    {
        return $this->belongsTo(Plan::class);
    }
}
