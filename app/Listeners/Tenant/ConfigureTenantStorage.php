<?php

namespace App\Listeners\Tenant;

use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Stancl\Tenancy\Events\TenancyInitialized;

class ConfigureTenantStorage
{
    /**
     * Handle the event.
     */
    public function handle(TenancyInitialized $event): void
    {
        $settings = DB::table('settings')->where('id', 1)->first();

        if (! $settings || ! isset($settings->storage_provider)) {
            return;
        }

        $storageProvider = $settings->storage_provider;
        $storageSettings = isset($settings->storage_settings) ? json_decode($settings->storage_settings, true) : null;

        if (! $storageSettings || ! isset($storageSettings[$storageProvider])) {
            return;
        }

        $config = $storageSettings[$storageProvider];

        if ($storageProvider === 's3' || $storageProvider === 'digitalocean') {
            Config::set('filesystems.disks.s3.key', $config['key'] ?? null);
            Config::set('filesystems.disks.s3.secret', $config['secret'] ?? null);
            Config::set('filesystems.disks.s3.region', $config['region'] ?? null);
            Config::set('filesystems.disks.s3.bucket', $config['bucket'] ?? null);
            Config::set('filesystems.disks.s3.url', $config['url'] ?? null);
            Config::set('filesystems.disks.s3.endpoint', $config['endpoint'] ?? null);
            Config::set('filesystems.disks.s3.use_path_style_endpoint', $config['use_path_style_endpoint'] ?? false);

            Config::set('media-library.disk_name', 's3');
        } else {
            // Default to public (local)
            Config::set('media-library.disk_name', 'public');
        }
    }
}
