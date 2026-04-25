<?php

namespace App\Http\Controllers\Tenant\Api\HR;

use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\Tenant\HR\StorePositionRequest;
use App\Http\Resources\Tenant\HR\PositionResource;
use App\Models\Tenant\HR\Position;
use App\Services\Tenant\HR\PositionService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Position Endpoints
 * * Handles management of HR job positions.
 */
class PositionController extends Controller
{
    public function __construct(
        private readonly PositionService $positionService
    ) {}

    /**
     * List all positions.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $perPage = $request->integer('per_page', 20);
        $positions = $this->positionService->getPaginatedPositions($request->all(), $perPage);

        return ApiResponse::success(
            data: PositionResource::collection($positions),
            message: 'Positions retrieved successfully',
            meta: ApiResponse::meta($positions)
        );
    }

    /**
     * Create a new position.
     *
     * @param StorePositionRequest $request
     * @return JsonResponse
     */
    public function store(StorePositionRequest $request): JsonResponse
    {
        $position = $this->positionService->createPosition($request->validated());

        return ApiResponse::success(
            new PositionResource($position),
            'Position created successfully',
            null,
            201
        );
    }

    /**
     * Show a specific position.
     *
     * @param int $id
     * @return JsonResponse
     */
    public function show(int $id): JsonResponse
    {
        $position = Position::query()->findOrFail($id);

        return ApiResponse::success(
            new PositionResource($position),
            'Position retrieved successfully'
        );
    }

    /**
     * Update an existing position.
     *
     * @param StorePositionRequest $request
     * @param int $id
     * @return JsonResponse
     */
    public function update(StorePositionRequest $request, int $id): JsonResponse
    {
        $position = Position::query()->findOrFail($id);
        $updatedPosition = $this->positionService->updatePosition($position, $request->validated());

        return ApiResponse::success(
            new PositionResource($updatedPosition),
            'Position updated successfully'
        );
    }

    /**
     * Delete a position.
     *
     * @param int $id
     * @return JsonResponse
     */
    public function destroy(int $id): JsonResponse
    {
        $position = Position::query()->findOrFail($id);
        $this->positionService->deletePosition($position);

        return ApiResponse::success(null, 'Position deleted successfully');
    }
}
