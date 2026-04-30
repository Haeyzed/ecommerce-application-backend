<?php

namespace App\Http\Controllers\Tenant\Api;

use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\Tenant\Setting\UpdateMailSettingRequest;
use App\Http\Resources\Tenant\MailSettingResource;
use App\Services\Tenant\MailSettingService;
use Illuminate\Http\JsonResponse;

/**
 * Mail Setting Endpoints
 * Handles retrieving and updating the tenant's SMTP configurations.
 */
class MailSettingController extends Controller
{
    /**
     * Initialize the controller with the MailSettingService.
     */
    public function __construct(
        private readonly MailSettingService $mailSettingService
    ) {}

    /**
     * Retrieve the current mail settings.
     */
    public function show(): JsonResponse
    {
        $settings = $this->mailSettingService->getCurrentSettings();

        return ApiResponse::success(
            new MailSettingResource($settings),
            'Mail settings retrieved successfully'
        );
    }

    /**
     * Update the mail settings.
     */
    public function update(UpdateMailSettingRequest $request): JsonResponse
    {
        $settings = $this->mailSettingService->updateSettings($request->validated());

        return ApiResponse::success(
            new MailSettingResource($settings),
            'Mail settings updated successfully'
        );
    }
}
