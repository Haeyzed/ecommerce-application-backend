<?php

namespace App\Http\Resources\Central;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PlanResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            /**
             * The unique identifier for the subscription plan.
             *
             * @var int $id
             *
             * @example 2
             */
            'id' => $this->id,

            /**
             * The display name of the plan.
             *
             * @var string $name
             *
             * @example "Pro Plan"
             */
            'name' => $this->name,

            /**
             * The URL-friendly identifier for the plan.
             *
             * @var string $slug
             *
             * @example "pro-plan"
             */
            'slug' => $this->slug,

            /**
             * The price of the plan in minor units (e.g., cents).
             *
             * @var int $price_cents
             *
             * @example 2900
             */
            'price_cents' => $this->price_cents,

            /**
             * The ISO currency code for the plan pricing.
             *
             * @var string $currency
             *
             * @example "USD"
             */
            'currency' => $this->currency,

            /**
             * The billing interval (e.g., monthly, yearly).
             *
             * @var string $interval
             *
             * @example "monthly"
             */
            'interval' => $this->interval,

            /**
             * An array listing the features included in the plan.
             *
             * @var array|null $features
             *
             * @example ["Unlimited Users", "Custom Domain", "Priority Support"]
             */
            'features' => $this->features,

            /**
             * A JSON object defining specific numerical limits for this plan.
             *
             * @var array|null $limits
             *
             * @example {"max_products": 5000, "max_storage_gb": 50}
             */
            'limits' => $this->limits,

            /**
             * Indicates if the plan is currently active and available for new subscriptions.
             *
             * @var bool $is_active
             *
             * @example true
             */
            'is_active' => (bool) $this->is_active,
        ];
    }
}
