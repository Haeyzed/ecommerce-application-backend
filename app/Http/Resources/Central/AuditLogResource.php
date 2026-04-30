<?php

namespace App\Http\Resources\Central;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AuditLogResource extends JsonResource
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
             * The unique identifier for the audit log entry.
             *
             * @var int $id
             *
             * @example 1024
             */
            'id' => $this->id,

            /**
             * The action or event that occurred (e.g., created, updated, deleted).
             *
             * @var string $event
             *
             * @example "updated"
             */
            'event' => $this->event,

            /**
             * The class name of the model that was audited.
             *
             * @var string $auditable_type
             *
             * @example "App\\Models\\Central\\Tenant"
             */
            'auditable_type' => $this->auditable_type,

            /**
             * The unique identifier of the model that was audited.
             *
             * @var int|string $auditable_id
             *
             * @example "tenant-xyz"
             */
            'auditable_id' => $this->auditable_id,

            /**
             * A JSON object representing the attributes of the model before the event.
             *
             * @var array|null $old_values
             *
             * @example {"status": "pending"}
             */
            'old_values' => $this->old_values,

            /**
             * A JSON object representing the attributes of the model after the event.
             *
             * @var array|null $new_values
             *
             * @example {"status": "active"}
             */
            'new_values' => $this->new_values,

            /**
             * The class name of the user or system responsible for the event.
             *
             * @var string|null $user_type
             *
             * @example "App\\Models\\User"
             */
            'user_type' => $this->user_type,

            /**
             * The identifier of the user or system responsible for the event.
             *
             * @var int|null $user_id
             *
             * @example 1
             */
            'user_id' => $this->user_id,

            /**
             * An array of tags associated with the audit log.
             *
             * @var array|null $tags
             *
             * @example ["onboarding", "tenant-activation"]
             */
            'tags' => $this->tags,

            /**
             * The date and time when the audit log was recorded.
             *
             * @var string $created_at
             *
             * @example "2026-04-25T14:00:00Z"
             */
            'created_at' => $this->created_at,
        ];
    }
}
