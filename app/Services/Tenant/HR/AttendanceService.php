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
     * @param  array  $filters  Query filters (e.g., employee_id, from, to)
     * @param  int  $perPage  Items per page
     */
    public function getPaginatedAttendances(array $filters = [], int $perPage = 30): LengthAwarePaginator
    {
        return Attendance::query()
            ->with('employee:id,first_name,last_name,employee_code')
            ->filter($filters)
            ->orderByDesc('date')
            ->paginate($perPage);
    }

    /**
     * Process an employee check-in.
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
            'minutes_worked' => $minutes,
        ]);

        return $attendance->fresh();
    }
}
