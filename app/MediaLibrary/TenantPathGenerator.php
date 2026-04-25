<?php

namespace App\MediaLibrary;

use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Spatie\MediaLibrary\Support\PathGenerator\PathGenerator;

/**
 * Class TenantPathGenerator
 *
 * Custom path generator for Spatie MediaLibrary to ensure tenant media files
 * do not collide in a shared storage bucket (e.g., AWS S3).
 */
class TenantPathGenerator implements PathGenerator
{
    /**
     * Get the tenant-specific prefix for the media paths.
     */
    protected function prefix(): string
    {
        $tenantId = function_exists('tenant') && tenant() ? tenant('id') : 'central';

        return "tenants/{$tenantId}";
    }

    /**
     * Get the path for the given media, relative to the root storage disk.
     */
    public function getPath(Media $media): string
    {
        return $this->prefix()."/media/{$media->id}/";
    }

    /**
     * Get the path for conversions of the given media, relative to the root storage disk.
     */
    public function getPathForConversions(Media $media): string
    {
        return $this->getPath($media).'conversions/';
    }

    /**
     * Get the path for responsive images of the given media, relative to the root storage disk.
     */
    public function getPathForResponsiveImages(Media $media): string
    {
        return $this->getPath($media).'responsive/';
    }
}
