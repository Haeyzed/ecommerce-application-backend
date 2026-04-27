<?php

namespace App\Http\Controllers\Tenant\Api\Notification;

use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\Tenant\StoreNotificationTemplateRequest;
use App\Http\Requests\Tenant\UpdateNotificationTemplateRequest;
use App\Http\Resources\Tenant\NotificationTemplateResource;
use App\Models\Tenant\NotificationTemplate;
use App\Services\Tenant\Notification\NotificationTemplateService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Notification Template Endpoints
 * * Handles CRUD operations for customizable system notification templates.
 */
class NotificationTemplateController extends Controller
{
    /**
     * Initialize the controller with the NotificationTemplateService.
     *
     * @param NotificationTemplateService $templateService
     */
    public function __construct(
        private readonly NotificationTemplateService $templateService
    ) {}

    /**
     * List all notification templates.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $perPage = $request->integer('per_page', 30);
        $templates = $this->templateService->getPaginatedTemplates($request->all(), $perPage);

        return ApiResponse::success(
            data: NotificationTemplateResource::collection($templates),
            message: 'Notification templates retrieved successfully',
            meta: ApiResponse::meta($templates)
        );
    }

    /**
     * Show a specific notification template.
     *
     * @param int $id The ID of the notification template.
     * @return JsonResponse
     */
    public function show(int $id): JsonResponse
    {
        $template = NotificationTemplate::query()->findOrFail($id);

        return ApiResponse::success(
            new NotificationTemplateResource($template),
            'Notification template retrieved successfully'
        );
    }

    /**
     * Create a new notification template.
     *
     * @param StoreNotificationTemplateRequest $request
     * @return JsonResponse
     */
    public function store(StoreNotificationTemplateRequest $request): JsonResponse
    {
        $template = $this->templateService->createTemplate($request->validated());

        return ApiResponse::success(
            new NotificationTemplateResource($template),
            'Notification template created successfully',
            null,
            201
        );
    }

    /**
     * Update an existing notification template.
     *
     * @param UpdateNotificationTemplateRequest $request
     * @param int $id The ID of the notification template.
     * @return JsonResponse
     */
    public function update(UpdateNotificationTemplateRequest $request, int $id): JsonResponse
    {
        $template = NotificationTemplate::query()->findOrFail($id);
        $updatedTemplate = $this->templateService->updateTemplate($template, $request->validated());

        return ApiResponse::success(
            new NotificationTemplateResource($updatedTemplate),
            'Notification template updated successfully'
        );
    }

    /**
     * Delete a notification template.
     *
     * @param int $id The ID of the notification template.
     * @return JsonResponse
     */
    public function destroy(int $id): JsonResponse
    {
        $template = NotificationTemplate::query()->findOrFail($id);
        $this->templateService->deleteTemplate($template);

        return ApiResponse::success(null, 'Notification template deleted successfully');
    }

    /**
     * Get the available placeholder variables for notification templates.
     *
     * @return JsonResponse
     */
    public function variables(): JsonResponse
    {
        $variables = $this->templateService->getAvailableVariables();

        return ApiResponse::success(
            $variables,
            'Available notification template variables retrieved successfully'
        );
    }
}
