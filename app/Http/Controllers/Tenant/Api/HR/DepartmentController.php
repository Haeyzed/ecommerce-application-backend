<?php

namespace App\Http\Controllers\Tenant\Api\HR;

use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\Tenant\HR\StoreDepartmentRequest;
use App\Http\Requests\Tenant\HR\UpdateDepartmentRequest;
use App\Http\Resources\Tenant\HR\DepartmentResource;
use App\Models\Tenant\HR\Department;
use App\Services\Tenant\HR\DepartmentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Department Endpoints
 * * Handles management of HR departments.
 */
class DepartmentController extends Controller
{
    public function __construct(
        private readonly DepartmentService $departmentService
    ) {}

    /**
     * List all departments.
     */
    public function index(Request $request): JsonResponse
    {
        $perPage = $request->integer('per_page', 20);

        $filters = [
            'search' => $request->string('search'),
            'parent_id' => $request->integer('parent_id'),
            'manager_employee_id' => $request->integer('manager_employee_id'),
        ];

        $departments = $this->departmentService->getPaginatedDepartments($filters, $perPage);

        return ApiResponse::success(
            data: DepartmentResource::collection($departments),
            message: 'Departments retrieved successfully',
            meta: ApiResponse::meta($departments)
        );
    }

    /**
     * Create a new department.
     */
    public function store(StoreDepartmentRequest $request): JsonResponse
    {
        $department = $this->departmentService->createDepartment($request->validated());

        return ApiResponse::success(
            new DepartmentResource($department),
            'Department created successfully',
            null,
            201
        );
    }

    /**
     * Show a specific department.
     */
    public function show(int $id): JsonResponse
    {
        $department = Department::query()->findOrFail($id);

        return ApiResponse::success(
            new DepartmentResource($department),
            'Department retrieved successfully'
        );
    }

    /**
     * Update an existing department.
     */
    public function update(UpdateDepartmentRequest $request, int $id): JsonResponse
    {
        $department = Department::query()->findOrFail($id);
        $updatedDepartment = $this->departmentService->updateDepartment($department, $request->validated());

        return ApiResponse::success(
            new DepartmentResource($updatedDepartment),
            'Department updated successfully'
        );
    }

    /**
     * Delete a department.
     */
    public function destroy(int $id): JsonResponse
    {
        $department = Department::query()->findOrFail($id);
        $this->departmentService->deleteDepartment($department);

        return ApiResponse::success(null, 'Department deleted successfully');
    }
}
