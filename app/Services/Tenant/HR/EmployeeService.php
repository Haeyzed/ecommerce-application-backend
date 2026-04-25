<?php

namespace App\Services\Tenant\HR;

use App\Models\Tenant\HR\Employee;
use DateTimeInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Str;

/**
 * Class EmployeeService
 * * Handles business logic related to tenant HR employees.
 */
class EmployeeService
{
    /**
     * Retrieve a paginated, filtered list of employees.
     *
     * @param array $filters Query filters (e.g., search, department_id, is_active).
     * @param int $perPage Items per page.
     * @return LengthAwarePaginator
     */
    public function getPaginatedEmployees(array $filters = [], int $perPage = 20): LengthAwarePaginator
    {
        return Employee::query()
            ->with(['department:id,name', 'position:id,title'])
            ->when($filters['department_id'] ?? null, fn ($q, $v) => $q->where('department_id', $v))
            ->when($filters['search'] ?? null, fn ($q, $v) => $q->where(fn ($qq) => $qq
                ->where('first_name', 'like', "%{$v}%")
                ->orWhere('last_name', 'like', "%{$v}%")
                ->orWhere('email', 'like', "%{$v}%")
                ->orWhere('employee_code', 'like', "%{$v}%")
            ))
            ->when(isset($filters['is_active']), fn ($q) => $q->where('is_active', (bool) $filters['is_active']))
            ->orderByDesc('id')
            ->paginate($perPage);
    }

    /**
     * Create a new employee record.
     *
     * @param array $data Validated employee data.
     * @return Employee
     */
    public function createEmployee(array $data): Employee
    {
        $data['employee_code'] = $data['employee_code'] ?? 'EMP-' . strtoupper(Str::random(6));

        return Employee::query()->create($data);
    }

    /**
     * Update an existing employee record.
     *
     * @param Employee $employee
     * @param array $data Validated update data.
     * @return Employee
     */
    public function updateEmployee(Employee $employee, array $data): Employee
    {
        $employee->update($data);
        return $employee->fresh();
    }

    /**
     * Delete an employee record.
     *
     * @param Employee $employee
     * @return void
     */
    public function deleteEmployee(Employee $employee): void
    {
        $employee->delete();
    }

    /**
     * Terminate an employee.
     *
     * @param Employee $employee
     * @param DateTimeInterface $when
     * @param string|null $reason
     * @return Employee
     */
    public function terminateEmployee(Employee $employee, DateTimeInterface $when, ?string $reason = null): Employee
    {
        $employee->update([
            'terminated_at' => $when,
            'is_active'     => false,
             'termination_reason' => $reason
        ]);

        return $employee->fresh();
    }
}
