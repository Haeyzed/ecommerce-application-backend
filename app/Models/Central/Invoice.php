<?php

namespace App\Models\Central;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;

/**
 * Class Invoice
 *
 * Represents a billing invoice generated for a tenant's subscription.
 *
 * @property int $id The unique identifier of the invoice.
 * @property string $tenant_id The foreign key referencing the tenant.
 * @property int|null $subscription_id The foreign key referencing the subscription.
 * @property string $number The unique invoice number.
 * @property string $amount The monetary amount of the invoice.
 * @property string $currency The ISO currency code.
 * @property string $status The current status of the invoice (e.g., open, paid).
 * @property Carbon|null $issued_at Timestamp of when the invoice was issued.
 * @property Carbon|null $paid_at Timestamp of when the invoice was paid.
 * @property Carbon|null $created_at Timestamp of when the invoice was created.
 * @property Carbon|null $updated_at Timestamp of when the invoice was last updated.
 * @property-read Tenant $tenant The tenant associated with the invoice.
 * @property-read Subscription|null $subscription The subscription this invoice belongs to.
 */
class Invoice extends Model implements AuditableContract
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
        'subscription_id',
        'number',
        'amount',
        'currency',
        'status',
        'issued_at',
        'paid_at',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'issued_at' => 'datetime',
            'paid_at' => 'datetime',
        ];
    }

    /**
     * Get the tenant that the invoice belongs to.
     */
    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    /**
     * Get the subscription associated with the invoice.
     */
    public function subscription(): BelongsTo
    {
        return $this->belongsTo(Subscription::class);
    }
}
