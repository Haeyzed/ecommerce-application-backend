<?php

namespace App\Http\Controllers\Tenant\Api\HR;

use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\Tenant\HR\StoreEmployeeRequest;
use App\Http\Requests\Tenant\HR\UpdateEmployeeRequest;
use App\Http\Resources\Tenant\HR\EmployeeResource;
use App\Models\Tenant\HR\Employee;
use App\Services\Tenant\HR\EmployeeService;
use DateMalformedStringException;
use DateTimeImmutable;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Spatie\MediaLibrary\MediaCollections\Exceptions\FileDoesNotExist;
use Spatie\MediaLibrary\MediaCollections\Exceptions\FileIsTooBig;
use Throwable;

/**
 * Employee Endpoints
 * * Handles management of employee records, avatars, and terminations.
 */
class EmployeeController extends Controller
{
    public function __construct(
        private readonly EmployeeService $employeeService
    ) {}

    /**
     * List all employees.
     */
    public function index(Request $request): JsonResponse
    {
        $perPage = $request->integer('per_page', 20);

        $filters = [
            'search' => $request->string('search'),
            'department_id' => $request->integer('department_id'),
        ];

        if ($request->has('is_active')) {
            $filters['is_active'] = $request->boolean('is_active');
        }

        $employees = $this->employeeService->getPaginatedEmployees($filters, $perPage);

        return ApiResponse::success(
            data: EmployeeResource::collection($employees),
            message: 'Employees retrieved successfully',
            meta: ApiResponse::meta($employees)
        );
    }

    /**
     * Create a new employee.
     *
     * @throws FileDoesNotExist
     * @throws FileIsTooBig|Throwable
     */
    public function store(StoreEmployeeRequest $request): JsonResponse
    {
        $employee = $this->employeeService->createEmployee($request->safe()->except(['avatar']));

        if ($request->hasFile('avatar')) {
            $employee->addMedia($request->file('avatar'))->toMediaCollection('default');
        }

        return ApiResponse::success(
            new EmployeeResource($employee->load(['department', 'position'])),
            'Employee created successfully',
            null,
            201
        );
    }

    /**
     * Show a specific employee.
     */
    public function show(int $id): JsonResponse
    {
        $employee = Employee::query()->findOrFail($id);

        return ApiResponse::success(
            new EmployeeResource($employee->load(['department', 'position'])),
            'Employee retrieved successfully'
        );
    }

    /**
     * Update an existing employee.
     * Note: Uses POST route in API to support multipart/form-data for avatar uploads.
     *
     * @throws FileDoesNotExist
     * @throws FileIsTooBig
     */
    public function update(UpdateEmployeeRequest $request, int $id): JsonResponse
    {
        $employee = Employee::query()->findOrFail($id);
        $updatedEmployee = $this->employeeService->updateEmployee($employee, $request->safe()->except(['avatar']));

        if ($request->hasFile('avatar')) {
            $updatedEmployee->clearMediaCollection('default')
                ->addMedia($request->file('avatar'))
                ->toMediaCollection('default');
        }

        return ApiResponse::success(
            new EmployeeResource($updatedEmployee->load(['department', 'position'])),
            'Employee updated successfully'
        );
    }

    /**
     * Delete an employee.
     */
    public function destroy(int $id): JsonResponse
    {
        $employee = Employee::query()->findOrFail($id);
        $this->employeeService->deleteEmployee($employee);

        return ApiResponse::success(null, 'Employee deleted successfully');
    }

    /**
     * Terminate an employee.
     *
     * @throws DateMalformedStringException
     */
    public function terminate(Request $request, int $id): JsonResponse
    {
        $request->validate(['date' => 'required|date', 'reason' => 'nullable|string']);

        $employee = Employee::query()->findOrFail($id);
        $terminatedEmployee = $this->employeeService->terminateEmployee(
            $employee,
            new DateTimeImmutable($request->input('date')),
            $request->input('reason')
        );

        return ApiResponse::success(
            new EmployeeResource($terminatedEmployee),
            'Employee terminated successfully'
        );
    }
}
