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
     * @param array $filters Query filters (e.g., search)
     * @param int $perPage Items per page
     * @return LengthAwarePaginator
     */
    public function getPaginatedDepartments(array $filters = [], int $perPage = 20): LengthAwarePaginator
    {
        return Department::query()
            ->withCount('employees')
            ->when($filters['search'] ?? null, fn ($q, $v) => $q->where('name', 'like', "%{$v}%"))
            ->orderBy('name')
            ->paginate($perPage);
    }

    /**
     * Create a new department.
     *
     * @param array $data Validated department data.
     * @return Department
     */
    public function createDepartment(array $data): Department
    {
        return Department::query()->create($data);
    }

    /**
     * Update an existing department.
     *
     * @param Department $department
     * @param array $data Validated update data.
     * @return Department
     */
    public function updateDepartment(Department $department, array $data): Department
    {
        $department->update($data);
        return $department->fresh();
    }

    /**
     * Delete a department.
     *
     * @param Department $department
     * @return void
     */
    public function deleteDepartment(Department $department): void
    {
        $department->delete();
    }
}
