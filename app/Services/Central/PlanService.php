<?php

namespace App\Services\Central;

use App\Models\Central\Plan;
use Illuminate\Database\Eloquent\Collection;

/**
 * Class PlanService
 * * Handles business logic related to subscription plans.
 */
class PlanService
{
    /**
     * Retrieve all plans ordered by price.
     *
     * @return Collection
     */
    public function getAllPlans(): Collection
    {
        return Plan::query()->orderBy('price_cents')->get();
    }

    /**
     * Create a new subscription plan.
     *
     * @param array $data Validated plan data.
     * @return Plan
     */
    public function createPlan(array $data): Plan
    {
        return Plan::query()->create($data);
    }

    /**
     * Retrieve a specific plan.
     *
     * @param Plan $plan
     * @return Plan
     */
    public function getPlanDetails(Plan $plan): Plan
    {
        return $plan;
    }

    /**
     * Update an existing subscription plan.
     *
     * @param Plan $plan
     * @param array $data Validated update data.
     * @return Plan
     */
    public function updatePlan(Plan $plan, array $data): Plan
    {
        $plan->update($data);

        return $plan->fresh();
    }

    /**
     * Delete a subscription plan.
     *
     * @param Plan $plan
     * @return void
     */
    public function deletePlan(Plan $plan): void
    {
        $plan->delete();
    }
}
