<?php

namespace App\Http\Controllers\Central\Api;

use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\Central\Plan\StorePlanRequest;
use App\Http\Requests\Central\Plan\UpdatePlanRequest;
use App\Models\Central\Plan;
use App\Services\Central\PlanService;
use Illuminate\Http\JsonResponse;

/**
 * Plan Endpoints
 * Handles CRUD operations for subscription plans.
 */
class PlanController extends Controller
{
    /**
     * Create a new PlanController instance.
     */
    public function __construct(
        private readonly PlanService $planService
    ) {}

    /**
     * List all plans.
     * Retrieves all available subscription plans ordered by price.
     */
    public function index(): JsonResponse
    {
        $plans = $this->planService->getAllPlans();

        return ApiResponse::success(
            ['plans' => $plans],
            'Plans retrieved successfully'
        );
    }

    /**
     * Create a new plan.
     * Adds a new subscription tier to the platform.
     */
    public function store(StorePlanRequest $request): JsonResponse
    {
        $plan = $this->planService->createPlan($request->validated());

        return ApiResponse::success(
            ['plan' => $plan],
            'Plan created successfully',
            null,
            201
        );
    }

    /**
     * Get plan details.
     * Retrieves a specific subscription plan.
     */
    public function show(Plan $plan): JsonResponse
    {
        $planDetails = $this->planService->getPlanDetails($plan);

        return ApiResponse::success(
            ['plan' => $planDetails],
            'Plan retrieved successfully'
        );
    }

    /**
     * Update a plan.
     * Modifies the features or pricing of an existing plan.
     */
    public function update(UpdatePlanRequest $request, Plan $plan): JsonResponse
    {
        $updatedPlan = $this->planService->updatePlan($plan, $request->validated());

        return ApiResponse::success(
            ['plan' => $updatedPlan],
            'Plan updated successfully'
        );
    }

    /**
     * Delete a plan.
     * Removes a subscription plan from the platform.
     */
    public function destroy(Plan $plan): JsonResponse
    {
        $this->planService->deletePlan($plan);

        return ApiResponse::success(null, 'Plan deleted successfully');
    }

    /**
     * List plans as dropdown options.
     *
     * Returns active plans with value (id) and label (name).
     */
    public function dropdown(): JsonResponse
    {
        $options = $this->planService->getDropdownOptions();

        return ApiResponse::success($options, 'Plan dropdown options retrieved successfully');
    }
}
