<?php

namespace App\Services\Tenant\HR;

use App\Models\Tenant\HR\Position;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

/**
 * Class PositionService
 * * Handles business logic related to tenant HR positions.
 */
class PositionService
{
    /**
     * Retrieve a paginated, filtered list of positions.
     *
     * @param  array  $filters  Query filters (e.g., department_id).
     * @param  int  $perPage  Items per page.
     */
    public function getPaginatedPositions(array $filters = [], int $perPage = 20): LengthAwarePaginator
    {
        return Position::query()
            ->with('department:id,name')
            ->when($filters['department_id'] ?? null, fn ($q, $v) => $q->where('department_id', $v))
            ->orderBy('title')
            ->paginate($perPage);
    }

    /**
     * Create a new position.
     *
     * @param  array  $data  Validated position data.
     */
    public function createPosition(array $data): Position
    {
        return Position::query()->create($data);
    }

    /**
     * Update an existing position.
     *
     * @param  array  $data  Validated update data.
     */
    public function updatePosition(Position $position, array $data): Position
    {
        $position->update($data);

        return $position->fresh();
    }

    /**
     * Delete a position.
     */
    public function deletePosition(Position $position): void
    {
        $position->delete();
    }
}
