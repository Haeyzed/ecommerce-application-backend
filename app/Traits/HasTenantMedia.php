<?php

namespace App\Traits;

use App\MediaLibrary\TenantPathGenerator;
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
            ->width(640)
            ->height(640)
            ->nonQueued();
    }

    /**
     * Register the default media collection with tenant-aware path generation.
     */
    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('default')
            ->useDisk(config('media-library.disk_name', env('MEDIA_DISK', 's3')))
            ->useFallbackUrl(asset('images/placeholder.png'))
            ->withResponsiveImages()
            ->registerMediaConversions(fn () => $this->registerMediaConversions());

        // Force an in-bucket prefix per tenant for the last added collection
        $this->mediaCollections[count($this->mediaCollections) - 1]
            ->pathGenerator(new TenantPathGenerator);
    }
}
