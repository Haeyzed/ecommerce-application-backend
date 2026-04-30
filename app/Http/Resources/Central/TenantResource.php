<?php

namespace App\Http\Resources\Central;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Resources\Json\JsonResource;

class TenantResource extends JsonResource
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
             * The unique identifier (string/slug) for the tenant.
             *
             * @var string $id
             *
             * @example "my-awesome-store"
             */
            'id' => $this->id,

            /**
             * The human-readable name of the tenant or store.
             *
             * @var string $name
             *
             * @example "My Awesome Store"
             */
            'name' => $this->name,

            /**
             * The operational status of the tenant (e.g., active, suspended, provisioning).
             *
             * @var string $status
             *
             * @example "active"
             */
            'status' => $this->status,

            /**
             * Additional contextual data specific to the tenant.
             *
             * @var array|null $data
             *
             * @example {"industry": "retail", "timezone": "America/New_York"}
             */
            'data' => $this->data,

            /**
             * The list of domains attached to this tenant.
             *
             * @var AnonymousResourceCollection|null $domains
             */
            'domains' => DomainResource::collection($this->whenLoaded('domains')),

            /**
             * The active subscription attached to this tenant.
             *
             * @var SubscriptionResource|null $subscription
             */
            'subscription' => new SubscriptionResource($this->whenLoaded('subscription')),

            /**
             * The date and time when the tenant was provisioned.
             *
             * @var string $created_at
             *
             * @example "2026-04-25T14:30:00Z"
             */
            'created_at' => $this->created_at,
        ];
    }
}
