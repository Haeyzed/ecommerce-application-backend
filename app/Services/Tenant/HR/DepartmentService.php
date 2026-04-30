<?php

namespace App\Services\Tenant\HR;

use App\Models\Tenant\HR\Department;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

/**
 * Class DepartmentService
 * * Handles business logic related to tenant HR departments.
 */
class DepartmentService
{
    /**
     * Retrieve a paginated, filtered list of departments.
     *
     * @param  array  $filters  Query filters (e.g., search)
     * @param  int  $perPage  Items per page
     */
    public function getPaginatedDepartments(array $filters = [], int $perPage = 20): LengthAwarePaginator
    {
        return Department::query()
            ->filter($filters)
            ->withCount('employees')
            ->orderBy('name')
            ->paginate($perPage);
    }

    /**
     * Create a new department.
     *
     * @param  array  $data  Validated department data.
     */
    public function createDepartment(array $data): Department
    {
        return Department::query()->create($data);
    }

    /**
     * Update an existing department.
     *
     * @param  array  $data  Validated update data.
     */
    public function updateDepartment(Department $department, array $data): Department
    {
        $department->update($data);

        return $department->fresh();
    }

    /**
     * Delete a department.
     */
    public function deleteDepartment(Department $department): void
    {
        $department->delete();
    }
}
