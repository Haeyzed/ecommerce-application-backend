<?php

namespace App\Models\Tenant\HR;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;

/**
 * Class LeaveRequest
 *
 * Represents an employee's request for time off (e.g., vacation, sick leave).
 *
 * @property int $id The unique identifier of the leave request.
 * @property int $employee_id The foreign key referencing the requesting employee.
 * @property string $type The type of leave (e.g., sick, vacation).
 * @property Carbon $start_date The start date of the leave.
 * @property Carbon $end_date The end date of the leave.
 * @property float $days The total calculated number of leave days.
 * @property string|null $reason The reason provided by the employee.
 * @property string $status The status of the request (e.g., pending, approved).
 * @property int|null $approved_by_employee_id The foreign key referencing the approving manager/employee.
 * @property Carbon|null $created_at Timestamp of when the request was created.
 * @property Carbon|null $updated_at Timestamp of when the request was last updated.
 * @property-read Employee $employee The employee who made the request.
 * @property-read Employee|null $approver The employee who approved/rejected the request.
 */
class LeaveRequest extends Model implements AuditableContract
{
    use Auditable;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'leave_requests';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'employee_id',
        'type',
        'start_date',
        'end_date',
        'days',
        'reason',
        'status',
        'approved_by_employee_id',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'start_date' => 'date',
            'end_date' => 'date',
            'days' => 'decimal:2',
        ];
    }

    /**
     * Get the employee who made the request.
     */
    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    /**
     * Get the employee who approved or rejected the request.
     */
    public function approver(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'approved_by_employee_id');
    }
}
