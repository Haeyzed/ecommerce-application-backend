<?php

namespace App\Http\Controllers\Tenant\Api\HR;

use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\Tenant\HR\StoreGoalRequest;
use App\Http\Requests\Tenant\HR\StorePerformanceReviewRequest;
use App\Http\Resources\Tenant\HR\GoalResource;
use App\Http\Resources\Tenant\HR\PerformanceReviewResource;
use App\Services\Tenant\HR\PerformanceService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Performance Endpoints
 * * Handles management of employee performance reviews and specific goals.
 */
class PerformanceController extends Controller
{
    public function __construct(
        private readonly PerformanceService $performanceService
    ) {}

    /**
     * List all performance reviews.
     */
    public function reviews(Request $request): JsonResponse
    {
        $perPage = $request->integer('per_page', 20);
        $reviews = $this->performanceService->getPaginatedReviews($request->all(), $perPage);

        return ApiResponse::success(
            data: PerformanceReviewResource::collection($reviews),
            message: 'Performance reviews retrieved successfully',
            meta: ApiResponse::meta($reviews)
        );
    }

    /**
     * Record a new performance review.
     */
    public function storeReview(StorePerformanceReviewRequest $request): JsonResponse
    {
        $review = $this->performanceService->createReview($request->validated());

        return ApiResponse::success(
            new PerformanceReviewResource($review),
            'Performance review created successfully',
            null,
            201
        );
    }

    /**
     * List all employee goals.
     */
    public function goals(Request $request): JsonResponse
    {
        $perPage = $request->integer('per_page', 20);
        $goals = $this->performanceService->getPaginatedGoals($request->all(), $perPage);

        return ApiResponse::success(
            data: GoalResource::collection($goals),
            message: 'Goals retrieved successfully',
            meta: ApiResponse::meta($goals)
        );
    }

    /**
     * Create a new employee goal.
     */
    public function storeGoal(StoreGoalRequest $request): JsonResponse
    {
        $goal = $this->performanceService->createGoal($request->validated());

        return ApiResponse::success(
            new GoalResource($goal),
            'Goal created successfully',
            null,
            201
        );
    }
}
