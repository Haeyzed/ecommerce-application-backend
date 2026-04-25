<?php

namespace App\Services\Tenant\HR;

use App\Models\Tenant\HR\Employee;
use App\Models\Tenant\Staff;
use App\Models\Tenant\User;
use DateTimeInterface;
use Illuminate\Auth\Events\Registered;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Throwable;

/**
 * Class EmployeeService
 * * Handles business logic related to tenant HR employees, auto-provisioning staff users.
 */
class EmployeeService
{
    /**
     * Retrieve a paginated, filtered list of employees.
     *
     * @param array $filters
     * @param int $perPage
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
     * Create a new employee record and automatically generate their User and Staff accounts.
     *
     * @param array $data Validated employee data.
     * @return Employee
     * @throws Throwable
     */
    public function createEmployee(array $data): Employee
    {
        return DB::transaction(function () use ($data) {
            $data['employee_code'] = $data['employee_code'] ?? 'EMP-' . strtoupper(Str::random(6));

            // Auto-provision User & Staff if no staff_id was provided
            if (empty($data['staff_id'])) {
                $user = User::query()->create([
                    'name'      => trim(($data['first_name'] ?? '') . ' ' . ($data['last_name'] ?? '')),
                    'email'     => $data['email'],
                    'password'  => Hash::make($data['password'] ?? Str::random(12)),
                    'is_active' => $data['is_active'] ?? true,
                ]);

                $staff = Staff::query()->create([
                    'user_id'   => $user->id,
                    'phone'     => $data['phone'] ?? null,
                    'currency'  => $data['currency'] ?? 'USD',
                    'locale'    => 'en',
                    'is_active' => $data['is_active'] ?? true,
                ]);

                $data['staff_id'] = $staff->id;

                // Dispatch Registered event to send the Welcome / Verify Email notification template
                event(new Registered($user));
            }

            // Remove the raw password before creating the HR Employee record
            unset($data['password']);

            return Employee::query()->create($data);
        });
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
        unset($data['password']); // Do not allow password updates via HR route directly
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
            'terminated_at'      => $when,
            'is_active'          => false,
            'termination_reason' => $reason,
        ]);

        // Automatically disable their login capabilities
        if ($employee->staff && $employee->staff->user) {
            $employee->staff->user->update(['is_active' => false]);
            $employee->staff->update(['is_active' => false]);
        }

        return $employee->fresh();
    }
}
