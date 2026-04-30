<?php

namespace App\Http\Requests\Central;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreNotificationTemplateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            /**
             * The event that triggers this template.
             *
             * @var string $event
             *
             * @example "tenant_subscribed"
             */
            'event' => ['required', 'string', 'max:60'],

            /**
             * The channel this template targets (e.g., email, sms).
             *
             * @var string $channel
             *
             * @example "email"
             */
            'channel' => ['required', 'string', 'max:30'],

            /**
             * The subject line of the notification.
             *
             * @var string|null $subject
             *
             * @example "Your subscription has been confirmed"
             */
            'subject' => ['nullable', 'string', 'max:255'],

            /**
             * The main body content, often with dynamic variables.
             *
             * @var string $body
             *
             * @example "Hello {tenant_name}, your subscription to {plan_name} has been processed."
             */
            'body' => ['required', 'string'],

            /**
             * Indicates if the template is active.
             *
             * @var bool|null $is_active
             *
             * @example true
             */
            'is_active' => ['boolean'],
        ];
    }
}
