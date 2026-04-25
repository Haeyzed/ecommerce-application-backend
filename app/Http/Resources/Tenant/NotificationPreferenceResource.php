<?php

namespace App\Http\Resources\Tenant;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class NotificationPreferenceResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            /**
             * The unique identifier for the notification preference.
             * @var int $id
             * @example 12
             */
            'id'              => $this->id,

            /**
             * The base class name of the entity receiving the notification (e.g., User, Employee).
             * @var string $notifiable_type
             * @example "Employee"
             */
            'notifiable_type' => class_basename($this->notifiable_type),

            /**
             * The unique identifier of the entity receiving the notification.
             * @var int|string $notifiable_id
             * @example 42
             */
            'notifiable_id'   => $this->notifiable_id,

            /**
             * The specific event this preference applies to.
             * @var string $event
             * @example "leave_request_approved"
             */
            'event'           => $this->event,

            /**
             * The notification channel (e.g., email, sms, database, push).
             * @var string $channel
             * @example "email"
             */
            'channel'         => $this->channel,

            /**
             * Indicates whether notifications for this event and channel are enabled.
             * @var bool $enabled
             * @example true
             */
            'enabled'         => (bool) $this->enabled,
        ];
    }
}
