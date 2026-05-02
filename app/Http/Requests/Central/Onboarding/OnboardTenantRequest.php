<?php

namespace App\Http\Requests\Central\Onboarding;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class OnboardTenantRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
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
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            /**
             * The name of the store/tenant.
             *
             * @var string $name
             *
             * @example "My Awesome Store"
             */
            'name' => ['required', 'string', 'max:255'],

            /**
             * The desired tenant subdomain.
             *
             * @var string $subdomain
             *
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
             *
             * @var string|null $owner_name
             *
             * @example "Jane Doe"
             */
            'owner_name' => ['nullable', 'string', 'max:255'],

            /**
             * The email of the store owner.
             *
             * @var string|null $owner_email
             *
             * @example "jane.doe@example.com"
             */
            'owner_email' => ['nullable', 'email'],

            /**
             * The password for the store owner.
             *
             * @var string|null $owner_password
             *
             * @example "SecurePassword123!"
             */
            'owner_password' => ['nullable', 'string', 'min:8'],

            /**
             * The default currency code.
             *
             * @var string|null $currency
             *
             * @example "USD"
             */
            'currency' => ['nullable', 'string', 'size:3'],

            /**
             * The default timezone.
             *
             * @var string|null $timezone
             *
             * @example "America/New_York"
             */
            'timezone' => ['nullable', 'string'],

            /**
             * The default language code.
             *
             * @var string|null $language
             *
             * @example "en"
             */
            'language' => ['nullable', 'string', 'size:2'],

            /**
             * The ID of the subscription plan.
             *
             * @var int|null $plan_id
             *
             * @example 2
             */
            'plan_id' => ['nullable', 'integer', 'exists:plans,id'],

            /**
             * The number of trial days.
             *
             * @var int|null $trial_days
             *
             * @example 14
             */
            'trial_days' => ['nullable', 'integer', 'min:1', 'max:90'],

            /**
             * A short tagline for the store.
             *
             * @var string|null $tagline
             *
             * @example "Your trusted online store"
             */
            'tagline' => ['nullable', 'string', 'max:255'],

            /**
             * The active storage provider.
             *
             * @var string|null $storage_provider
             *
             * @example "public"
             */
            'storage_provider' => ['nullable', 'string', Rule::in(['public', 's3', 'digitalocean'])],

            /**
             * Storage provider configuration keyed by provider name.
             *
             * Structure follows the tenant settings seed format:
             * ```
             * {
             *   "public":        { "enabled": true },
             *   "s3":            { "enabled": false, "key": "…", "secret": "…", "region": "…", "bucket": "…", "url": null, "endpoint": null, "use_path_style_endpoint": false },
             *   "digitalocean":  { "enabled": false, "key": "…", "secret": "…", "region": "…", "bucket": "…", "url": null, "endpoint": "…", "use_path_style_endpoint": false }
             * }
             * ```
             *
             * @var array|null $storage_settings
             */
            'storage_settings' => ['nullable', 'array'],
            'storage_settings.public' => ['nullable', 'array'],
            'storage_settings.public.enabled' => ['nullable', 'boolean'],
            'storage_settings.s3' => ['nullable', 'array'],
            'storage_settings.s3.enabled' => ['nullable', 'boolean'],
            'storage_settings.s3.key' => ['nullable', 'string'],
            'storage_settings.s3.secret' => ['nullable', 'string'],
            'storage_settings.s3.region' => ['nullable', 'string'],
            'storage_settings.s3.bucket' => ['nullable', 'string'],
            'storage_settings.s3.url' => ['nullable', 'string'],
            'storage_settings.s3.endpoint' => ['nullable', 'string'],
            'storage_settings.s3.use_path_style_endpoint' => ['nullable', 'boolean'],
            'storage_settings.digitalocean' => ['nullable', 'array'],
            'storage_settings.digitalocean.enabled' => ['nullable', 'boolean'],
            'storage_settings.digitalocean.key' => ['nullable', 'string'],
            'storage_settings.digitalocean.secret' => ['nullable', 'string'],
            'storage_settings.digitalocean.region' => ['nullable', 'string'],
            'storage_settings.digitalocean.bucket' => ['nullable', 'string'],
            'storage_settings.digitalocean.url' => ['nullable', 'string'],
            'storage_settings.digitalocean.endpoint' => ['nullable', 'string'],
            'storage_settings.digitalocean.use_path_style_endpoint' => ['nullable', 'boolean'],

            /**
             * Payment provider configuration keyed by provider name.
             *
             * Structure follows the tenant settings seed format:
             * ```
             * {
             *   "stripe":      { "enabled": false, "test_mode": true, "test": { "public_key": "…", "secret_key": "…", "webhook_secret": "…" }, "live": { … } },
             *   "paypal":      { "enabled": false, "test_mode": true, "test": { "client_id": "…", "secret": "…" }, "live": { … } },
             *   "paystack":    { "enabled": false, "test_mode": true, "test": { "public_key": "…", "secret_key": "…" }, "live": { … } },
             *   "flutterwave": { "enabled": false, "test_mode": true, "test": { "public_key": "…", "secret_key": "…", "encryption_key": "…" }, "live": { … } },
             *   "razorpay":    { "enabled": false, "test_mode": true, "test": { "key_id": "…", "key_secret": "…" }, "live": { … } },
             *   "square":      { "enabled": false, "test_mode": true, "test": { "application_id": "…", "access_token": "…", "location_id": "…" }, "live": { … } }
             * }
             * ```
             *
             * @var array|null $payment_providers
             */
            'payment_providers' => ['nullable', 'array'],
            'payment_providers.*' => ['nullable', 'array'],
            'payment_providers.*.enabled' => ['nullable', 'boolean'],
            'payment_providers.*.test_mode' => ['nullable', 'boolean'],
            'payment_providers.*.test' => ['nullable', 'array'],
            'payment_providers.*.live' => ['nullable', 'array'],
        ];
    }
}
