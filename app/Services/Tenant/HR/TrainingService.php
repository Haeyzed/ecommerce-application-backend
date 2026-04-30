<?php

namespace App\Services\Tenant\HR;

use App\Models\Tenant\HR\Employee;
use App\Models\Tenant\HR\Training;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

/**
 * Class TrainingService
 * * Handles business logic related to employee training and compliance modules.
 */
class TrainingService
{
    /**
     * Retrieve a paginated, filtered list of training sessions.
     *
     * @param  array  $filters  Query filters (e.g., is_mandatory).
     * @param  int  $perPage  Items per page.
     */
    public function getPaginatedTrainings(array $filters = [], int $perPage = 20): LengthAwarePaginator
    {
        return Training::query()
            ->withCount('employees')
            ->when($filters['is_mandatory'] ?? null, fn ($q) => $q->where('is_mandatory', true))
            ->orderByDesc('starts_at')
            ->paginate($perPage);
    }

    /**
     * Create a new training session.
     *
     * @param  array  $data  Validated training data.
     */
    public function createTraining(array $data): Training
    {
        return Training::query()->create($data);
    }

    /**
     * Update an existing training session.
     *
     * @param  array  $data  Validated update data.
     */
    public function updateTraining(Training $training, array $data): Training
    {
        $training->update($data);

        return $training->fresh();
    }

    /**
     * Enroll an employee into a training session.
     */
    public function enrollEmployee(Training $training, Employee $employee): void
    {
        $training->employees()->syncWithoutDetaching([$employee->id => ['status' => 'enrolled']]);
    }

    /**
     * Mark a training session as completed for an employee.
     */
    public function completeTraining(Training $training, Employee $employee): void
    {
        $training->employees()->updateExistingPivot($employee->id, ['status' => 'completed', 'completed_at' => now()]);
    }

    /**
     * Delete a training session.
     */
    public function deleteTraining(Training $training): void
    {
        $training->delete();
    }
}
