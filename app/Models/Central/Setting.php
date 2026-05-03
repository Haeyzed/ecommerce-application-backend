<?php

namespace App\Models\Central;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia; // Use InteractsWithMedia directly
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Spatie\Image\Enums\Fit; // Import for media conversions

/**
 * Class Setting
 *
 * Represents the central platform's configuration, branding, and defaults.
 * Typically operates as a single-row table for the central application.
 *
 * @property int $id The unique identifier of the settings record.
 * @property string $name The name of the central platform.
 * @property string|null $tagline The platform tagline or slogan.
 * @property string $currency The default currency for the platform (e.g., for billing).
 * @property string $timezone The default timezone for the central platform.
 * @property string $language The default language for the central platform.
 * @property string|null $primary_color The primary branding color hex code.
 * @property array|null $social JSON array of social media links for the central platform.
 * @property string|null $contact_email The public contact email for the central platform.
 * @property string|null $contact_phone The public contact phone number.
 * @property string|null $address The physical or mailing address of the central platform.
 * @property Carbon|null $created_at Timestamp of when the settings were created.
 * @property Carbon|null $updated_at Timestamp of when the settings were last updated.
 */
class Setting extends Model implements AuditableContract, HasMedia
{
    use Auditable, InteractsWithMedia;

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
        'primary_color',
        'social',
        'contact_email',
        'contact_phone',
        'address',
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
        ];
    }

    /**
     * Register default media conversions for the model.
     */
    public function registerMediaConversions(?Media $media = null): void
    {
        $this->addMediaConversion('thumb')
            ->width(160)
            ->height(160)
            ->sharpen(8)
            ->nonQueued();

        $this->addMediaConversion('preview')
            ->fit(Fit::Contain, 640, 640)
            ->nonQueued();
    }
}
