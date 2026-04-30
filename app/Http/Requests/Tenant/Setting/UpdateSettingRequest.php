<?php

namespace App\Http\Requests\Tenant\Setting;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\UploadedFile;

class UpdateSettingRequest extends FormRequest
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
             * The name of the store.
             *
             * @var string|null $name
             *
             * @example "My Awesome Store"
             */
            'name' => ['sometimes', 'string', 'max:255'],

            /**
             * The store's tagline or slogan.
             *
             * @var string|null $tagline
             *
             * @example "Best products in the world"
             */
            'tagline' => ['nullable', 'string', 'max:255'],

            /**
             * The default ISO currency code.
             *
             * @var string|null $currency
             *
             * @example "USD"
             */
            'currency' => ['sometimes', 'string', 'size:3'],

            /**
             * The default timezone.
             *
             * @var string|null $timezone
             *
             * @example "UTC"
             */
            'timezone' => ['sometimes', 'string', 'max:64'],

            /**
             * The default language code.
             *
             * @var string|null $language
             *
             * @example "en"
             */
            'language' => ['sometimes', 'string', 'max:8'],

            /**
             * The primary hex color code for the store UI.
             *
             * @var string|null $primary_color
             *
             * @example "#FF5733"
             */
            'primary_color' => ['nullable', 'string', 'max:16'],

            /**
             * JSON array of social media links.
             *
             * @var array|null $social
             *
             * @example {"facebook": "url", "twitter": "url"}
             */
            'social' => ['nullable', 'array'],

            /**
             * JSON array of payment provider configurations.
             *
             * @var array|null $payment_providers
             *
             * @example {"stripe": {"public_key": "..."}}
             */
            'payment_providers' => ['nullable', 'array'],

            /**
             * The public contact email.
             *
             * @var string|null $contact_email
             *
             * @example "hello@example.com"
             */
            'contact_email' => ['nullable', 'email', 'max:255'],

            /**
             * The public contact phone number.
             *
             * @var string|null $contact_phone
             *
             * @example "+1234567890"
             */
            'contact_phone' => ['nullable', 'string', 'max:32'],

            /**
             * The physical address of the store.
             *
             * @var string|null $address
             *
             * @example "123 Main St, City, Country"
             */
            'address' => ['nullable', 'string'],

            /**
             * The store logo image.
             *
             * @var UploadedFile|null $logo
             */
            'logo' => ['nullable', 'file', 'image', 'max:2048'],

            /**
             * The store favicon image.
             *
             * @var UploadedFile|null $favicon
             */
            'favicon' => ['nullable', 'file', 'image', 'max:512'],
        ];
    }
}
