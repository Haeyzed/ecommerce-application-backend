<?php

namespace App\Http\Requests\Central\Setting;

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
             * The name of the central platform.
             *
             * @var string|null $name
             *
             * @example "Ecommerce Central Platform"
             */
            'name' => ['sometimes', 'string', 'max:255'],

            /**
             * The platform's tagline or slogan.
             *
             * @var string|null $tagline
             *
             * @example "Manage all your stores in one place"
             */
            'tagline' => ['nullable', 'string', 'max:255'],

            /**
             * The default ISO currency code for the platform (e.g., for billing).
             *
             * @var string|null $currency
             *
             * @example "USD"
             */
            'currency' => ['sometimes', 'string', 'size:3'],

            /**
             * The default timezone for the central platform.
             *
             * @var string|null $timezone
             *
             * @example "UTC"
             */
            'timezone' => ['sometimes', 'string', 'max:64'],

            /**
             * The default language code for the central platform.
             *
             * @var string|null $language
             *
             * @example "en"
             */
            'language' => ['sometimes', 'string', 'max:8'],

            /**
             * The primary hex color code for the central platform UI.
             *
             * @var string|null $primary_color
             *
             * @example "#007bff"
             */
            'primary_color' => ['nullable', 'string', 'max:16'],

            /**
             * JSON array of social media links for the central platform.
             *
             * @var array|null $social
             *
             * @example {"facebook": "url", "twitter": "url"}
             */
            'social' => ['nullable', 'array'],

            /**
             * The public contact email for the central platform.
             *
             * @var string|null $contact_email
             *
             * @example "support@example.com"
             */
            'contact_email' => ['nullable', 'email', 'max:255'],

            /**
             * The public contact phone number for the central platform.
             *
             * @var string|null $contact_phone
             *
             * @example "+1234567890"
             */
            'contact_phone' => ['nullable', 'string', 'max:32'],

            /**
             * The physical address of the central platform's office.
             *
             * @var string|null $address
             *
             * @example "456 Corporate Ave, Metropolis"
             */
            'address' => ['nullable', 'string'],

            /**
             * The central platform logo image.
             *
             * @var UploadedFile|null $logo
             */
            'logo' => ['nullable', 'file', 'image', 'max:2048'],

            /**
             * The central platform favicon image.
             *
             * @var UploadedFile|null $favicon
             */
            'favicon' => ['nullable', 'file', 'image', 'max:512'],
        ];
    }
}
