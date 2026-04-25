<?php

namespace App\Models\Tenant\HR;

use App\Models\Tenant\Staff;
use App\Traits\Auditable;
use App\Traits\HasTenantMedia;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;
use Spatie\MediaLibrary\HasMedia;

/**
 * Class Employee
 *
 * Represents a staff member inside the tenant's HR system.
 *
 * @property int $id The unique identifier of the employee.
 * @property int|null $staff_id The linked Staff account ID (if they have login access).
 * @property int|null $department_id The ID of the department the employee belongs to.
 * @property int|null $position_id The ID of the employee's job position.
 * @property string $employee_code A unique internal tracking code for the employee.
 * @property string $first_name The employee's first name.
 * @property string $last_name The employee's last name.
 * @property string $email The employee's email address.
 * @property string|null $phone The employee's phone number.
 * @property Carbon|null $hired_at The date the employee was hired.
 * @property Carbon|null $terminated_at The date the employee was terminated.
 * @property string $employment_type The type of employment (e.g., full_time, part_time).
 * @property int $salary_cents The employee's salary in minor units (cents).
 * @property string $currency The ISO currency code for the salary.
 * @property bool $is_active Indicates if the employee is currently active.
 * @property Carbon|null $created_at Timestamp of when the record was created.
 * @property Carbon|null $updated_at Timestamp of when the record was last updated.
 * @property-read Staff|null $staff The system user account tied to this employee.
 * @property-read Department|null $department The department this employee belongs to.
 * @property-read Position|null $position The position held by this employee.
 * @property-read Collection|Attendance[] $attendances The attendance records of the employee.
 * @property-read Collection|LeaveRequest[] $leaveRequests The leave requests made by the employee.
 * @property-read Collection|Payslip[] $payslips The payslips issued to the employee.
 * @property-read Collection|PerformanceReview[] $reviews The performance reviews of the employee.
 * @property-read Collection|Goal[] $goals The goals assigned to the employee.
 * @property-read Collection|Training[] $trainings The training courses the employee is enrolled in.
 * @property-read Collection|EmployeeDocument[] $documents The documents attached to the employee.
 */
class Employee extends Model implements AuditableContract, HasMedia
{
    use Auditable, HasTenantMedia;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'staff_id',
        'department_id',
        'position_id',
        'employee_code',
        'first_name',
        'last_name',
        'email',
        'phone',
        'hired_at',
        'terminated_at',
        'termination_reason',
        'employment_type',
        'salary_cents',
        'currency',
        'is_active',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'hired_at' => 'date',
            'terminated_at' => 'date',
            'salary_cents' => 'integer',
            'is_active' => 'boolean',
        ];
    }

    public function staff(): BelongsTo
    {
        return $this->belongsTo(Staff::class, 'staff_id');
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    public function position(): BelongsTo
    {
        return $this->belongsTo(Position::class);
    }

    public function attendances(): HasMany
    {
        return $this->hasMany(Attendance::class);
    }

    public function leaveRequests(): HasMany
    {
        return $this->hasMany(LeaveRequest::class);
    }

    public function payslips(): HasMany
    {
        return $this->hasMany(Payslip::class);
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(PerformanceReview::class);
    }

    public function goals(): HasMany
    {
        return $this->hasMany(Goal::class);
    }

    public function trainings(): BelongsToMany
    {
        return $this->belongsToMany(Training::class, 'employee_training')
            ->withPivot('status', 'completed_at')
            ->withTimestamps();
    }

    public function documents(): HasMany
    {
        return $this->hasMany(EmployeeDocument::class);
    }
}
