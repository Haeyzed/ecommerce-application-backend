<?php

namespace App\Services\Tenant\HR;

use App\Models\Tenant\HR\Employee;
use App\Models\Tenant\Staff;
use App\Models\Tenant\User;
use App\Notifications\Tenant\DynamicTemplateNotification;
use App\Services\Tenant\SettingService;
use DateTimeInterface;
use Illuminate\Auth\Events\Registered;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Throwable;

/**
 * Class EmployeeService
 * * Handles business logic related to tenant HR employees, auto-provisioning admin users.
 */
class EmployeeService
{
    public function __construct(
        private readonly SettingService $settingService
    ) {}

    /**
     * Retrieve a paginated, filtered list of employees.
     *
     * @param  array  $filters  Query filters (e.g., search, department_id, is_active).
     * @param  int  $perPage  Items per page.
     */
    public function getPaginatedEmployees(array $filters = [], int $perPage = 20): LengthAwarePaginator
    {
        return Employee::query()
            ->with(['department:id,name', 'position:id,title'])
            ->filter($filters)
            ->orderByDesc('id')
            ->paginate($perPage);
    }

    /**
     * Create a new employee record and automatically generate their User and Staff accounts.
     *
     * @param  array  $data  Validated employee data.
     *
     * @throws Throwable
     */
    public function createEmployee(array $data): Employee
    {
        return DB::transaction(function () use ($data) {
            $data['employee_code'] = $data['employee_code'] ?? 'EMP-'.strtoupper(Str::random(6));

            if (empty($data['staff_id'])) {

                $rawPassword = $data['password'] ?? Str::random(8);

                $user = User::query()->create([
                    'name' => trim(($data['first_name'] ?? '').' '.($data['last_name'] ?? '')),
                    'email' => $data['email'],
                    'password' => Hash::make($rawPassword),
                    'user_type' => 'admin',
                    'is_active' => $data['is_active'] ?? true,
                ]);

                $staff = Staff::query()->create([
                    'user_id' => $user->id,
                    'phone' => $data['phone'] ?? null,
                    'currency' => $data['currency'] ?? 'USD',
                    'locale' => 'en',
                    'is_active' => $data['is_active'] ?? true,
                ]);

                $data['staff_id'] = $staff->id;

                $storeName = $this->settingService->getCurrentSettings()->name ?? config('app.name');

                event(new Registered($user));

                $user->notify(new DynamicTemplateNotification(
                    event: 'admin_registered',
                    templateData: [
                        'name' => $user->name,
                        'store_name' => $storeName,
                        'email' => $user->email,
                        'password' => $rawPassword,
                    ]
                ));
            }

            unset($data['password']);

            return Employee::query()->create($data);
        });
    }

    /**
     * Update an existing employee record.
     *
     * @param  array  $data  Validated update data.
     */
    public function updateEmployee(Employee $employee, array $data): Employee
    {
        unset($data['password']); // Do not allow password updates via HR route directly
        $employee->update($data);

        return $employee->fresh();
    }

    /**
     * Delete an employee record.
     */
    public function deleteEmployee(Employee $employee): void
    {
        $employee->delete();
    }

    /**
     * Terminate an employee.
     */
    public function terminateEmployee(Employee $employee, DateTimeInterface $when, ?string $reason = null): Employee
    {
        $employee->update([
            'terminated_at' => $when,
            'is_active' => false,
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
