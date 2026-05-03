<?php

namespace App\Http\Controllers\Central\Api;

use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\Central\Setting\UpdateSettingRequest;
use App\Http\Resources\Central\SettingResource;
use App\Services\Central\SettingService;
use Illuminate\Http\JsonResponse;
use Throwable;

/**
 * Central Setting Endpoints
 * Handles the retrieval and updating of central platform settings.
 */
class SettingController extends Controller
{
    /**
     * Create a new SettingController instance.
     */
    public function __construct(
        private readonly SettingService $settingService
    ) {
        $this->middleware('permission:view central settings')->only(['show']);
        $this->middleware('permission:update central settings')->only(['update']);
    }

    /**
     * Retrieve the current central platform settings.
     */
    public function show(): JsonResponse
    {
        $setting = $this->settingService->getCurrentSettings();

        return ApiResponse::success(
            new SettingResource($setting),
            'Central settings retrieved successfully'
        );
    }

    /**
     * Update the central platform settings (supports multipart/form-data for image uploads).
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
            new SettingResource($setting),
            'Central settings updated successfully'
        );
    }
}
