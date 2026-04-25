<?php

namespace App\Services\Tenant;

use App\Models\Tenant\Setting;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Throwable;

/**
 * Class SettingService
 * * Handles business logic related to tenant storefront settings and configurations.
 */
class SettingService
{
    /**
     * Retrieve the current store settings.
     * * Creates a default settings row if one does not exist.
     */
    public function getCurrentSettings(): Setting
    {
        return Setting::query()->firstOrCreate([], [
            'name' => function_exists('tenant') && tenant() ? tenant('name') ?? 'My Store' : 'My Store',
            'currency' => 'USD',
            'timezone' => 'UTC',
            'language' => 'en',
        ]);
    }

    /**
     * Update the store settings and manage associated media uploads.
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
