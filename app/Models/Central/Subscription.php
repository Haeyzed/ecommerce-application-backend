<?php

namespace App\Models\Central;

use App\Enums\Central\SubscriptionStatus;
use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;

/**
 * Class Subscription
 *
 * Represents a tenant's billing subscription plan within the central platform.
 *
 * @property int $id The unique identifier of the subscription.
 * @property string $tenant_id The foreign key referencing the tenant.
 * @property int $plan_id The foreign key referencing the subscription plan.
 * @property string $status The current status of the subscription.
 * @property Carbon|null $trial_ends_at Timestamp of when the trial period ends.
 * @property Carbon|null $current_period_start Timestamp of when the current billing cycle started.
 * @property Carbon|null $current_period_end Timestamp of when the current billing cycle ends.
 * @property Carbon|null $cancelled_at Timestamp of when the subscription was cancelled.
 * @property Carbon|null $created_at Timestamp of when the subscription was created.
 * @property Carbon|null $updated_at Timestamp of when the subscription was last updated.
 * @property-read Tenant $tenant The tenant that owns the subscription.
 * @property-read Plan $plan The plan associated with this subscription.
 * @property-read Collection|Invoice[] $invoices The invoices generated for this subscription.
 */
class Subscription extends Model implements AuditableContract
{
    use Auditable;

    /**
     * The database connection that should be used by the model.
     *
     * @var string
     */
    protected $connection = 'central';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'tenant_id',
        'plan_id',
        'status',
        'trial_ends_at',
        'current_period_start',
        'current_period_end',
        'cancelled_at',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'status' => SubscriptionStatus::class,
            'trial_ends_at' => 'datetime',
            'current_period_start' => 'datetime',
            'current_period_end' => 'datetime',
            'cancelled_at' => 'datetime',
        ];
    }

    /**
     * Get the tenant that owns the subscription.
     */
    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    /**
     * Get the plan associated with the subscription.
     */
    public function plan(): BelongsTo
    {
        return $this->belongsTo(Plan::class);
    }

    /**
     * Get the invoices generated for this subscription.
     */
    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class);
    }

    /**
     * Check if the subscription is currently active or trialing.
     */
    public function isActive(): bool
    {
        return in_array($this->status, [
            SubscriptionStatus::ACTIVE->value,
            SubscriptionStatus::TRIAL->value,
        ], true);
    }
}
