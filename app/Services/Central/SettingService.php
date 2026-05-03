<?php

namespace App\Services\Central;

use App\Models\Central\Setting; // Assuming a Central Setting model exists
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Throwable;

/**
 * Class SettingService
 * Handles business logic related to central platform settings and configurations.
 */
class SettingService
{
    /**
     * Retrieve the current central platform settings.
     * Creates a default settings row if one does not exist.
     */
    public function getCurrentSettings(): Setting
    {
        return Setting::query()->firstOrCreate([], [
            'name' => 'Ecommerce Central Platform',
            'currency' => 'USD',
            'timezone' => 'UTC',
            'language' => 'en',
        ]);
    }

    /**
     * Update the central platform settings and manage associated media uploads.
     *
     * @param  array  $data  Validated settings data.
     * @param  UploadedFile|null  $logo  Optional logo upload.
     * @param  UploadedFile|null  $favicon  Optional favicon upload.
     *
     * @throws Throwable
     */
    public function updateSettings(array $data, ?UploadedFile $logo = null, ?UploadedFile $favicon = null): Setting
    {
        return DB::transaction(function () use ($data, $logo, $favicon) {
            $settings = $this->getCurrentSettings();

            $settings->update($data);

            if ($logo) {
                $settings->clearMediaCollection('logo')
                    ->addMedia($logo)
                    ->toMediaCollection('logo');
            }

            if ($favicon) {
                $settings->clearMediaCollection('favicon')
                    ->addMedia($favicon)
                    ->toMediaCollection('favicon');
            }

            return $settings->fresh();
        });
    }
}
