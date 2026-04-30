<?php

namespace App\Http\Resources\Central;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class NotificationTemplateResource extends JsonResource
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
             * The unique identifier for the notification template.
             *
             * @var int $id
             *
             * @example 5
             */
            'id' => $this->id,

            /**
             * The event that triggers this notification template.
             *
             * @var string $event
             *
             * @example "plan_expiring"
             */
            'event' => $this->event,

            /**
             * The channel this template is designed for (e.g., email, sms, push).
             *
             * @var string $channel
             *
             * @example "email"
             */
            'channel' => $this->channel,

            /**
             * The subject line of the notification (typically used for emails).
             *
             * @var string|null $subject
             *
             * @example "Action Required: Your plan is expiring soon"
             */
            'subject' => $this->subject,

            /**
             * The main content body of the notification, often containing variable placeholders.
             *
             * @var string $body
             *
             * @example "Hello {tenant_name}, your subscription is expiring on {expiration_date}."
             */
            'body' => $this->body,

            /**
             * Indicates whether this template is currently active and in use.
             *
             * @var bool $is_active
             *
             * @example true
             */
            'is_active' => (bool) $this->is_active,
        ];
    }
}
