<?php

namespace App\Http\Resources\Central;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SubscriptionResource extends JsonResource
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
             * The unique identifier for the subscription.
             *
             * @var int $id
             *
             * @example 45
             */
            'id' => $this->id,

            /**
             * The identifier of the tenant that owns this subscription.
             *
             * @var string $tenant_id
             *
             * @example "tenant-xyz"
             */
            'tenant_id' => $this->tenant_id,

            /**
             * The ID of the billing plan attached to this subscription.
             *
             * @var int $plan_id
             *
             * @example 2
             */
            'plan_id' => $this->plan_id,

            /**
             * The fully loaded plan relationship for the subscription.
             *
             * @var PlanResource|null $plan
             */
            'plan' => new PlanResource($this->whenLoaded('plan')),

            /**
             * The current status of the subscription (e.g., active, past_due, canceled).
             *
             * @var string $status
             *
             * @example "active"
             */
            'status' => $this->status,

            /**
             * The payment gateway or provider managing the subscription.
             *
             * @var string|null $provider
             *
             * @example "stripe"
             */
            'provider' => $this->provider,

            /**
             * The date and time when the free trial period ends, if applicable.
             *
             * @var string|null $trial_ends_at
             *
             * @example "2026-05-10T00:00:00Z"
             */
            'trial_ends_at' => $this->trial_ends_at,

            /**
             * The date and time when the current billing cycle concludes.
             *
             * @var string|null $current_period_ends_at
             *
             * @example "2026-05-25T14:30:00Z"
             */
            'current_period_ends_at' => $this->current_period_ends_at,

            /**
             * The date and time when the subscription is scheduled to cancel.
             *
             * @var string|null $cancels_at
             *
             * @example null
             */
            'cancels_at' => $this->cancels_at,
        ];
    }
}
