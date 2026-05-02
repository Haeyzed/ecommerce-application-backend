<?php

namespace App\Traits;

use Spatie\Image\Enums\Fit;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

/**
 * Trait HasTenantMedia
 *
 * Wrapper around Spatie MediaLibrary that ensures media is stored in tenant-isolated directories.
 * Automatically registers a default collection and standard image conversions (thumb, preview).
 */
trait HasTenantMedia
{
    use InteractsWithMedia;

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
