<?php

namespace App\Services\Central;

use App\Models\Central\Plan;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Collection as SupportCollection;

/**
 * Class PlanService
 * * Handles business logic related to subscription plans.
 */
class PlanService
{
    /**
     * Retrieve all plans ordered by price.
     */
    public function getAllPlans(): Collection
    {
        return Plan::query()->orderBy('price_cents')->get();
    }

    /**
     * Create a new subscription plan.
     *
     * @param  array  $data  Validated plan data.
     */
    public function createPlan(array $data): Plan
    {
        return Plan::query()->create($data);
    }

    /**
     * Retrieve a specific plan.
     */
    public function getPlanDetails(Plan $plan): Plan
    {
        return $plan;
    }

    /**
     * Update an existing subscription plan.
     *
     * @param  array  $data  Validated update data.
     */
    public function updatePlan(Plan $plan, array $data): Plan
    {
        $plan->update($data);

        return $plan->fresh();
    }

    /**
     * Delete a subscription plan.
     */
    public function deletePlan(Plan $plan): void
    {
        $plan->delete();
    }

    /**
     * Retrieve active plans as dropdown options.
     *
     * @return SupportCollection<int, array{value: int, label: string}>
     */
    public function getDropdownOptions(): SupportCollection
    {
        return Plan::query()
            ->where('is_active', true)
            ->orderBy('name')
            ->get(['id', 'name'])
            ->map(fn (Plan $plan): array => [
                'value' => $plan->id,
                'label' => $plan->name,
            ]);
    }
}
