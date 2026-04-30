<?php

namespace App\Models\Tenant\HR;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;

/**
 * Class Attendance
 *
 * Tracks the daily check-in and check-out records of an employee.
 *
 * @property int $id The unique identifier of the attendance record.
 * @property int $employee_id The foreign key referencing the employee.
 * @property Carbon $date The specific date of the attendance record.
 * @property Carbon|null $check_in The timestamp when the employee checked in.
 * @property Carbon|null $check_out The timestamp when the employee checked out.
 * @property int $minutes_worked The total calculated minutes worked for the day.
 * @property string $status The attendance status (e.g., present, absent, late).
 * @property string|null $note Any manual notes added to the attendance record.
 * @property Carbon|null $created_at Timestamp of when the record was created.
 * @property Carbon|null $updated_at Timestamp of when the record was last updated.
 * @property-read Employee $employee The employee this attendance record belongs to.
 */
class Attendance extends Model implements AuditableContract
{
    use Auditable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'employee_id',
        'date',
        'check_in',
        'check_out',
        'minutes_worked',
        'status',
        'note',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'date' => 'date',
            'check_in' => 'datetime',
            'check_out' => 'datetime',
            'minutes_worked' => 'int',
        ];
    }

    /**
     * Get the employee associated with the attendance record.
     */
    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    /**
     * Scope a query to apply a dynamic array of filters.
     */
    public function scopeFilter(Builder $query, array $filters): void
    {
        $query->when($filters['employee_id'] ?? null, function (Builder $query, int $employeeId) {
            $query->where('employee_id', $employeeId);
        })
            ->when($filters['from'] ?? null, function (Builder $query, string $from) {
                $query->whereDate('date', '>=', $from);
            })
            ->when($filters['to'] ?? null, function (Builder $query, string $to) {
                $query->whereDate('date', '<=', $to);
            });
    }
}
