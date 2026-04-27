<?php

namespace App\Http\Resources\Tenant;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SettingResource extends JsonResource
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
             * The unique identifier for the settings record.
             * @var int $id
             * @example 1
             */
            'id'                => $this->id,

            /**
             * The name of the store.
             * @var string $name
             * @example "My Awesome Store"
             */
            'name'              => $this->name,

            /**
             * The store's tagline or slogan.
             * @var string|null $tagline
             * @example "Best products in the world"
             */
            'tagline'           => $this->tagline,

            /**
             * The default ISO currency code.
             * @var string $currency
             * @example "USD"
             */
            'currency'          => $this->currency,

            /**
             * The default timezone.
             * @var string $timezone
             * @example "UTC"
             */
            'timezone'          => $this->timezone,

            /**
             * The default language code.
             * @var string $language
             * @example "en"
             */
            'language'          => $this->language,

            /**
             * The primary hex color code for the store UI.
             * @var string|null $primary_color
             * @example "#FF5733"
             */
            'primary_color'     => $this->primary_color,

            /**
             * JSON object of social media links.
             * @var array|null $social
             * @example {"facebook": "url"}
             */
            'social'            => $this->social,

            /**
             * JSON object of payment providers configurations.
             * @var array|null $payment_providers
             * @example {"stripe": {"enabled": true}}
             */
            'payment_providers' => $this->payment_providers,

            /**
             * The public contact email.
             * @var string|null $contact_email
             * @example "hello@example.com"
             */
            'contact_email'     => $this->contact_email,

            /**
             * The public contact phone number.
             * @var string|null $contact_phone
             * @example "+1234567890"
             */
            'contact_phone'     => $this->contact_phone,

            /**
             * The physical address of the store.
             * @var string|null $address
             * @example "123 Main St, City, Country"
             */
            'address'           => $this->address,

            /**
             * The full URL of the uploaded logo image.
             * @var string|null $logo_url
             * @example "https://example.com/media/logo.png"
             */
            'logo_url'          => method_exists($this->resource, 'getFirstMediaUrl') ? $this->getFirstMediaUrl('logo') : $this->logo_path,

            /**
             * The full URL of the uploaded favicon image.
             * @var string|null $favicon_url
             * @example "https://example.com/media/favicon.ico"
             */
            'favicon_url'       => method_exists($this->resource, 'getFirstMediaUrl') ? $this->getFirstMediaUrl('favicon') : $this->favicon_path,
        ];
    }
}
