<?php

namespace App\Http\Requests\Tenant;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateNotificationPreferencesRequest extends FormRequest
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
             * A list of notification preference updates.
             *
             * @var array $preferences
             *
             * @example [{"event": "leave_approved", "channel": "email", "enabled": true}]
             */
            'preferences' => ['required', 'array'],

            /**
             * The specific event the preference applies to.
             *
             * @var string $preferences .*.event
             *
             * @example "leave_approved"
             */
            'preferences.*.event' => ['required', 'string', 'max:60'],

            /**
             * The channel for the notification.
             *
             * @var string $preferences .*.channel
             *
             * @example "email"
             */
            'preferences.*.channel' => ['required', 'string', 'max:30'],

            /**
             * Indicates if notifications for this event and channel are enabled.
             *
             * @var bool $preferences .*.enabled
             *
             * @example true
             */
            'preferences.*.enabled' => ['required', 'boolean'],
        ];
    }
}
