<?php

namespace App\Models\Tenant\HR;

use App\Traits\Auditable;
use App\Traits\HasTenantMedia;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;
use Spatie\MediaLibrary\HasMedia;

/**
 * Class EmployeeDocument
 *
 * Represents an uploaded document (contract, ID, cert) attached to an employee.
 *
 * @property int $id The unique identifier of the document record.
 * @property int $employee_id The foreign key referencing the employee.
 * @property string $title The title or name of the document.
 * @property string|null $type The type of document (e.g., contract, ID, certificate).
 * @property Carbon|null $expires_at The expiration date of the document.
 * @property string|null $notes Additional notes regarding the document.
 * @property Carbon|null $created_at Timestamp of when the document record was created.
 * @property Carbon|null $updated_at Timestamp of when the document record was last updated.
 * @property-read Employee $employee The employee this document belongs to.
 */
class EmployeeDocument extends Model implements AuditableContract, HasMedia
{
    use Auditable, HasTenantMedia;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'employee_id',
        'title',
        'type',
        'expires_at',
        'notes',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'expires_at' => 'date',
        ];
    }

    /**
     * Scope a query to filter employee documents.
     *
     * @param  Builder  $query
     * @return Builder
     */
    public function scopeFilter($query, array $filters)
    {
        return $query
            ->when($filters['employee_id'] ?? null, fn ($q, $v) => $q->where('employee_id', $v))
            ->when($filters['expiring_within_days'] ?? null, fn ($q, $v) => $q->whereNotNull('expires_at')->whereDate('expires_at', '<=', now()->addDays((int) $v)));
    }

    /**
     * Get the employee this document belongs to.
     *
     * @return BelongsTo<Employee, EmployeeDocument>
     */
    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }
}
