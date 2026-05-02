<?php

namespace App\Support\MediaLibrary;
use Spatie\MediaLibrary\Support\UrlGenerator\DefaultUrlGenerator;

class TenantAwareUrlGenerator extends DefaultUrlGenerator
{
    /**
     * Create a new class instance.
     */
    public function getUrl(): string
    {
        $url = asset($this->getPathRelativeToRoot());

        return $this->versionUrl($url);
    }
}
