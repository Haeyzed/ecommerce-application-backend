<?php

namespace App\Services\Tenant\HR;

use App\Models\Tenant\HR\Attendance;
use App\Models\Tenant\HR\Employee;
use Carbon\Carbon;
use DateTimeInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

/**
 * Class AttendanceService
 * * Handles business logic related to employee attendance.
 */
class AttendanceService
{
    /**
     * Retrieve a paginated, filtered list of attendance records.
     *
     * @param array $filters Query filters (e.g., employee_id, from, to)
     * @param int $perPage Items per page
     * @return LengthAwarePaginator
     */
    public function getPaginatedAttendances(array $filters = [], int $perPage = 30): LengthAwarePaginator
    {
        return Attendance::query()
            ->with('employee:id,first_name,last_name,employee_code')
            ->when($filters['employee_id'] ?? null, fn ($q, $v) => $q->where('employee_id', $v))
            ->when($filters['from'] ?? null, fn ($q, $v) => $q->whereDate('date', '>=', $v))
            ->when($filters['to'] ?? null, fn ($q, $v) => $q->whereDate('date', '<=', $v))
            ->orderByDesc('date')
            ->paginate($perPage);
    }

    /**
     * Process an employee check-in.
     *
     * @param Employee $employee
     * @param DateTimeInterface $at
     * @return Attendance
     */
    public function checkInEmployee(Employee $employee, DateTimeInterface $at): Attendance
    {
        $date = Carbon::instance($at)->toDateString();

        return Attendance::query()->updateOrCreate(
            ['employee_id' => $employee->id, 'date' => $date],
            ['check_in' => $at, 'status' => 'present']
        );
    }

    /**
     * Process an employee check-out.
     *
     * @param Employee $employee
     * @param DateTimeInterface $at
     * @return Attendance
     */
    public function checkOutEmployee(Employee $employee, DateTimeInterface $at): Attendance
    {
        $date = Carbon::instance($at)->toDateString();
        $attendance = Attendance::query()
            ->where(['employee_id' => $employee->id, 'date' => $date])
            ->firstOrFail();

        $minutes = $attendance->check_in ? Carbon::instance($at)->diffInMinutes($attendance->check_in) : 0;

        $attendance->update([
            'check_out' => $at,
            'minutes_worked' => $minutes
        ]);

        return $attendance->fresh();
    }
}
