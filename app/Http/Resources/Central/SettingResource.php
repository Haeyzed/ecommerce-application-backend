<?php

namespace App\Http\Resources\Central;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SettingResource extends JsonResource
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
             * The unique identifier for the central settings record.
             *
             * @var int $id
             *
             * @example 1
             */
            'id' => $this->id,

            /**
             * The name of the central platform.
             *
             * @var string $name
             *
             * @example "Ecommerce Central Platform"
             */
            'name' => $this->name,

            /**
             * The platform's tagline or slogan.
             *
             * @var string|null $tagline
             *
             * @example "Manage all your stores in one place"
             */
            'tagline' => $this->tagline,

            /**
             * The default ISO currency code for the platform (e.g., for billing).
             *
             * @var string $currency
             *
             * @example "USD"
             */
            'currency' => $this->currency,

            /**
             * The default timezone for the central platform.
             *
             * @var string $timezone
             *
             * @example "UTC"
             */
            'timezone' => $this->timezone,

            /**
             * The default language code for the central platform.
             *
             * @var string $language
             *
             * @example "en"
             */
            'language' => $this->language,

            /**
             * The primary hex color code for the central platform UI.
             *
             * @var string|null $primary_color
             *
             * @example "#007bff"
             */
            'primary_color' => $this->primary_color,

            /**
             * JSON object of social media links for the central platform.
             *
             * @var array|null $social
             *
             * @example {"facebook": "url"}
             */
            'social' => $this->social,

            /**
             * The public contact email for the central platform.
             *
             * @var string|null $contact_email
             *
             * @example "support@example.com"
             */
            'contact_email' => $this->contact_email,

            /**
             * The public contact phone number for the central platform.
             *
             * @var string|null $contact_phone
             *
             * @example "+1234567890"
             */
            'contact_phone' => $this->contact_phone,

            /**
             * The physical address of the central platform's office.
             *
             * @var string|null $address
             *
             * @example "456 Corporate Ave, Metropolis"
             */
            'address' => $this->address,

            /**
             * The full URL of the uploaded logo image.
             *
             * @var string|null $logo_url
             *
             * @example "https://example.com/media/central_logo.png"
             */
            'logo_url' => method_exists($this->resource, 'getFirstMediaUrl') ? $this->getFirstMediaUrl('logo') : $this->logo_path,

            /**
             * The full URL of the uploaded favicon image.
             *
             * @var string|null $favicon_url
             *
             * @example "https://example.com/media/central_favicon.ico"
             */
            'favicon_url' => method_exists($this->resource, 'getFirstMediaUrl') ? $this->getFirstMediaUrl('favicon') : $this->favicon_path,
        ];
    }
}
