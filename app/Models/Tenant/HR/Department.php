<?php

namespace App\Models\Tenant\HR;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;

/**
 * Class Department
 *
 * Represents an organizational unit or grouping within the tenant's company.
 *
 * @property int $id The unique identifier of the department.
 * @property string $name The display name of the department.
 * @property string|null $code An optional internal code for the department.
 * @property int|null $parent_id The ID of the parent department (for hierarchies).
 * @property int|null $manager_employee_id The ID of the employee who manages this department.
 * @property Carbon|null $created_at Timestamp of when the department was created.
 * @property Carbon|null $updated_at Timestamp of when the department was last updated.
 * @property-read Collection|Employee[] $employees The employees belonging to this department.
 * @property-read Employee|null $manager The employee designated as the department manager.
 */
class Department extends Model implements AuditableContract
{
    use Auditable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'code',
        'parent_id',
        'manager_employee_id',
    ];

    /**
     * Get the employees associated with the department.
     */
    public function employees(): HasMany
    {
        return $this->hasMany(Employee::class);
    }

    /**
     * Get the manager of the department.
     */
    public function manager(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'manager_employee_id');
    }
}
