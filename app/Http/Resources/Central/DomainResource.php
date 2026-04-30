<?php

namespace App\Http\Resources\Central;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DomainResource extends JsonResource
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
             * The unique identifier for the domain record.
             *
             * @var int $id
             *
             * @example 15
             */
            'id' => $this->id,

            /**
             * The fully qualified domain name or subdomain string.
             *
             * @var string $domain
             *
             * @example "store.example.com"
             */
            'domain' => $this->domain,

            /**
             * The identifier of the tenant this domain is mapped to.
             *
             * @var string $tenant_id
             *
             * @example "tenant-foo-bar"
             */
            'tenant_id' => $this->tenant_id,

            /**
             * The date and time when the domain was registered.
             *
             * @var string $created_at
             *
             * @example "2026-04-25T14:30:00Z"
             */
            'created_at' => $this->created_at,
        ];
    }
}
