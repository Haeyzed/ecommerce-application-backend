<?php

namespace App\Http\Requests\Tenant\Setting;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\UploadedFile;

/**
 * @property string|null $name The name of the store.
 * @property string|null $tagline The store tagline.
 * @property string|null $currency The default currency.
 * @property string|null $timezone The default timezone.
 * @property string|null $language The default language.
 * @property string|null $primary_color The primary branding hex color.
 * @property array|null $social Array of social media links.
 * @property array|null $payment_providers Array of active payment providers.
 * @property string|null $contact_email The public contact email.
 * @property string|null $contact_phone The public contact phone.
 * @property string|null $address The physical store address.
 * @property UploadedFile|null $logo The logo image file.
 * @property UploadedFile|null $favicon The favicon image file.
 */
class UpdateSettingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['sometimes', 'required', 'string', 'max:255'],
            'tagline' => ['sometimes', 'nullable', 'string', 'max:255'],
            'currency' => ['sometimes', 'required', 'string', 'max:8'],
            'timezone' => ['sometimes', 'required', 'string', 'max:64'],
            'language' => ['sometimes', 'required', 'string', 'max:8'],
            'primary_color' => ['sometimes', 'nullable', 'string', 'max:16'],
            'social' => ['sometimes', 'nullable', 'array'],
            'payment_providers' => ['sometimes', 'nullable', 'array'],
            'contact_email' => ['sometimes', 'nullable', 'email'],
            'contact_phone' => ['sometimes', 'nullable', 'string'],
            'address' => ['sometimes', 'nullable', 'string'],
            'logo' => ['sometimes', 'nullable', 'image', 'max:2048'],
            'favicon' => ['sometimes', 'nullable', 'image', 'max:1024'],
        ];
    }
}
