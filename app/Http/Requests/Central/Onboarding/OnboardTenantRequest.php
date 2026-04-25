<?php

namespace App\Http\Requests\Central\Onboarding;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class OnboardTenantRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        if ($this->filled('subdomain')) {
            $this->merge([
                'subdomain' => str($this->input('subdomain'))->slug()->toString(),
            ]);
        }
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            /**
             * The name of the store/tenant.
             * @var string $name
             * @example "My Awesome Store"
             */
            'name' => ['required', 'string', 'max:255'],

            /**
             * The desired tenant subdomain.
             * @var string $subdomain
             * @example "my-awesome-store"
             */
            'subdomain' => [
                'required',
                'string',
                'min:3',
                'max:63',
                'regex:/^[a-z0-9](?:[a-z0-9-]*[a-z0-9])?$/',
                Rule::notIn([
                    'www',
                    'api',
                    'admin',
                    'app',
                    'mail',
                    'ftp',
                    'support',
                    'docs',
                    'help',
                    'status',
                    'dashboard',
                    'localhost',
                ]),
                Rule::unique('tenants', 'id'),
            ],

            /**
             * The name of the store owner.
             * @var string|null $owner_name
             * @example "Jane Doe"
             */
            'owner_name' => ['nullable', 'string', 'max:255'],

            /**
             * The email of the store owner.
             * @var string|null $owner_email
             * @example "jane.doe@example.com"
             */
            'owner_email' => ['nullable', 'email'],

            /**
             * The password for the store owner.
             * @var string|null $owner_password
             * @example "SecurePassword123!"
             */
            'owner_password' => ['nullable', 'string', 'min:8'],

            /**
             * The default currency code.
             * @var string|null $currency
             * @example "USD"
             */
            'currency' => ['nullable', 'string', 'size:3'],

            /**
             * The default timezone.
             * @var string|null $timezone
             * @example "America/New_York"
             */
            'timezone' => ['nullable', 'string'],

            /**
             * The default language code.
             * @var string|null $language
             * @example "en"
             */
            'language' => ['nullable', 'string', 'size:2'],

            /**
             * The ID of the subscription plan.
             * @var int|null $plan_id
             * @example 2
             */
            'plan_id' => ['nullable', 'integer', 'exists:plans,id'],

            /**
             * The number of trial days.
             * @var int|null $trial_days
             * @example 14
             */
            'trial_days' => ['nullable', 'integer', 'min:1', 'max:90'],
        ];
    }
}
