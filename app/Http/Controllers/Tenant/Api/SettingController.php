<?php

namespace App\Http\Controllers\Tenant\Api;

use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\Tenant\Setting\UpdateSettingRequest;
use App\Services\Tenant\SettingService;
use Illuminate\Http\JsonResponse;
use Throwable;

/**
 * Setting Endpoints
 * * Handles the retrieval and updating of tenant storefront settings.
 */
class SettingController extends Controller
{
    /**
     * Create a new SettingController instance.
     */
    public function __construct(
        private readonly SettingService $settingService
    ) {}

    /**
     * Get current store settings.
     */
    public function show(): JsonResponse
    {
        $setting = $this->settingService->getCurrentSettings();

        return ApiResponse::success(
            ['setting' => $setting],
            'Settings retrieved successfully'
        );
    }

    /**
     * Update store settings.
     *
     * @throws Throwable
     */
    public function update(UpdateSettingRequest $request): JsonResponse
    {
        $setting = $this->settingService->updateSettings(
            $request->safe()->except(['logo', 'favicon']),
            $request->file('logo'),
            $request->file('favicon')
        );

        return ApiResponse::success(
            ['setting' => $setting],
            'Settings updated successfully'
        );
    }
}
