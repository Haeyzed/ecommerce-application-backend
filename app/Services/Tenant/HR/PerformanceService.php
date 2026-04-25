<?php

namespace App\Services\Tenant\HR;

use App\Models\Tenant\HR\Goal;
use App\Models\Tenant\HR\PerformanceReview;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

/**
 * Class PerformanceService
 * * Handles business logic related to employee performance reviews and goals.
 */
class PerformanceService
{
    /**
     * Retrieve a paginated list of performance reviews.
     *
     * @param array $filters Query filters (e.g., employee_id).
     * @param int $perPage Items per page.
     * @return LengthAwarePaginator
     */
    public function getPaginatedReviews(array $filters = [], int $perPage = 20): LengthAwarePaginator
    {
        return PerformanceReview::query()
            ->with(['employee:id,first_name,last_name', 'reviewer:id,first_name,last_name'])
            ->when($filters['employee_id'] ?? null, fn ($q, $v) => $q->where('employee_id', $v))
            ->orderByDesc('period_end')
            ->paginate($perPage);
    }

    /**
     * Create a new performance review.
     *
     * @param array $data Validated review data.
     * @return PerformanceReview
     */
    public function createReview(array $data): PerformanceReview
    {
        return PerformanceReview::query()->create($data);
    }

    /**
     * Retrieve a paginated list of employee goals.
     *
     * @param array $filters Query filters (e.g., employee_id, status).
     * @param int $perPage Items per page.
     * @return LengthAwarePaginator
     */
    public function getPaginatedGoals(array $filters = [], int $perPage = 20): LengthAwarePaginator
    {
        return Goal::query()
            ->when($filters['employee_id'] ?? null, fn ($q, $v) => $q->where('employee_id', $v))
            ->when($filters['status'] ?? null, fn ($q, $v) => $q->where('status', $v))
            ->orderByDesc('id')
            ->paginate($perPage);
    }

    /**
     * Create a new employee goal.
     *
     * @param array $data Validated goal data.
     * @return Goal
     */
    public function createGoal(array $data): Goal
    {
        return Goal::query()->create($data);
    }
}
