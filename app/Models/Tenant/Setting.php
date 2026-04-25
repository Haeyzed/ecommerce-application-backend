<?php

namespace App\Models\Tenant;

use App\Traits\Auditable;
use App\Traits\HasTenantMedia;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;
use Spatie\MediaLibrary\HasMedia;

/**
 * Class Setting
 *
 * Represents the per-tenant storefront configuration, branding, and defaults.
 * Typically operates as a single-row table per tenant.
 *
 * @property int $id The unique identifier of the settings record.
 * @property string $name The name of the store.
 * @property string|null $tagline The store tagline or slogan.
 * @property string $currency The default store currency.
 * @property string $timezone The default store timezone.
 * @property string $language The default store language.
 * @property string|null $logo_path Direct path to the logo (if not using MediaLibrary).
 * @property string|null $favicon_path Direct path to the favicon (if not using MediaLibrary).
 * @property string|null $primary_color The primary branding color hex code.
 * @property array|null $social JSON array of social media links.
 * @property array|null $payment_providers JSON array of configured payment providers.
 * @property string|null $contact_email The public contact email for the store.
 * @property string|null $contact_phone The public contact phone number.
 * @property string|null $address The physical or mailing address of the store.
 * @property Carbon|null $created_at Timestamp of when the settings were created.
 * @property Carbon|null $updated_at Timestamp of when the settings were last updated.
 */
class Setting extends Model implements AuditableContract, HasMedia
{
    use Auditable, HasTenantMedia;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'tagline',
        'currency',
        'timezone',
        'language',
        'logo_path',
        'favicon_path',
        'primary_color',
        'social',
        'payment_providers',
        'contact_email',
        'contact_phone',
        'address',
        'id',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'social' => 'array',
            'payment_providers' => 'array',
        ];
    }
}
