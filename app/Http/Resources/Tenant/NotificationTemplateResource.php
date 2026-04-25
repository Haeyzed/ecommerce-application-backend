<?php

namespace App\Http\Resources\Tenant;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class NotificationTemplateResource extends JsonResource
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
             * The unique identifier for the notification template.
             * @var int $id
             * @example 5
             */
            'id'        => $this->id,

            /**
             * The event that triggers this notification template.
             * @var string $event
             * @example "payroll_processed"
             */
            'event'     => $this->event,

            /**
             * The channel this template is designed for (e.g., email, sms, push).
             * @var string $channel
             * @example "email"
             */
            'channel'   => $this->channel,

            /**
             * The subject line of the notification (typically used for emails).
             * @var string|null $subject
             * @example "Your Payslip for {month} is Ready"
             */
            'subject'   => $this->subject,

            /**
             * The main content body of the notification, often containing variable placeholders.
             * @var string $body
             * @example "Hello {first_name}, your payslip of {net_amount} has been processed."
             */
            'body'      => $this->body,

            /**
             * Indicates whether this template is currently active and in use.
             * @var bool $is_active
             * @example true
             */
            'is_active' => (bool) $this->is_active,
        ];
    }
}
