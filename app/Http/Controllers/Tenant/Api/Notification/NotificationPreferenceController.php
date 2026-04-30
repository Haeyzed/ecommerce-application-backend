<?php

namespace App\Http\Controllers\Tenant\Api\Notification;

use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\Tenant\UpdateNotificationPreferencesRequest;
use App\Http\Resources\Tenant\NotificationPreferenceResource;
use App\Services\Tenant\Notification\NotificationPreferenceService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Throwable;

/**
 * Notification Preference Endpoints
 * * Handles the management of user and staff notification opt-ins and opt-outs.
 */
class NotificationPreferenceController extends Controller
{
    /**
     * Initialize the controller with the NotificationPreferenceService.
     */
    public function __construct(
        private readonly NotificationPreferenceService $preferenceService
    ) {}

    /**
     * Get the authenticated user's notification preferences.
     */
    public function index(Request $request): JsonResponse
    {
        $notifiable = $request->user('customer') ?? $request->user('staff');

        if (! $notifiable) {
            return ApiResponse::error('Unauthorized', 401);
        }

        $preferences = $this->preferenceService->getPreferencesFor($notifiable);

        return ApiResponse::success(
            NotificationPreferenceResource::collection($preferences),
            'Notification preferences retrieved successfully'
        );
    }

    /**
     * Update the authenticated user's notification preferences.
     *
     * @throws Throwable
     */
    public function update(UpdateNotificationPreferencesRequest $request): JsonResponse
    {
        $notifiable = $request->user('customer') ?? $request->user('staff');

        if (! $notifiable) {
            return ApiResponse::error('Unauthorized', 401);
        }

        $this->preferenceService->updatePreferencesFor($notifiable, $request->validated()['preferences']);
        $updatedPreferences = $this->preferenceService->getPreferencesFor($notifiable);

        return ApiResponse::success(
            NotificationPreferenceResource::collection($updatedPreferences),
            'Notification preferences updated successfully'
        );
    }
}
