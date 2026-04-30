<?php

namespace App\Http\Controllers\Central\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Central\StoreNotificationTemplateRequest;
use App\Http\Requests\Central\UpdateNotificationPreferencesRequest;
use App\Http\Requests\Central\UpdateNotificationTemplateRequest;
use App\Http\Resources\Central\NotificationPreferenceResource;
use App\Http\Resources\Central\NotificationTemplateResource;
use App\Models\Central\NotificationTemplate;
use App\Services\Central\NotificationPreferenceService;
use App\Services\Central\NotificationTemplateService;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Response;
use Throwable;

class NotificationController extends Controller
{
    public function __construct(
        private readonly NotificationTemplateService $templateService,
        private readonly NotificationPreferenceService $preferenceService
    ) {}

    /**
     * Get all notification templates.
     */
    public function index(Request $request): JsonResource
    {
        $templates = $this->templateService->getPaginatedTemplates($request->all());

        return NotificationTemplateResource::collection($templates);
    }

    /**
     * Get a specific notification template.
     */
    public function showTemplate(NotificationTemplate $template): JsonResource
    {
        return new NotificationTemplateResource($template);
    }

    /**
     * Create a new notification template.
     */
    public function store(StoreNotificationTemplateRequest $request): JsonResource
    {
        $template = $this->templateService->createTemplate($request->validated());

        return new NotificationTemplateResource($template);
    }

    /**
     * Update a notification template.
     */
    public function update(UpdateNotificationTemplateRequest $request, NotificationTemplate $template): JsonResource
    {
        $template = $this->templateService->updateTemplate($template, $request->validated());

        return new NotificationTemplateResource($template);
    }

    /**
     * Delete a notification template.
     */
    public function destroy(NotificationTemplate $template): Response
    {
        $this->templateService->deleteTemplate($template);

        return response()->noContent();
    }

    /**
     * Get available template variables.
     */
    public function getTemplateVariables(): Response
    {
        return response($this->templateService->getAvailableVariables());
    }

    /**
     * Get user's notification preferences.
     */
    public function getPreferences(Request $request): JsonResource
    {
        $preferences = $this->preferenceService->getPreferencesFor($request->user());

        return NotificationPreferenceResource::collection($preferences);
    }

    /**
     * Update user's notification preferences.
     *
     * @throws Throwable
     */
    public function updatePreferences(UpdateNotificationPreferencesRequest $request): JsonResource
    {
        $this->preferenceService->updatePreferencesFor($request->user(), $request->validated('preferences'));
        $preferences = $this->preferenceService->getPreferencesFor($request->user());

        return NotificationPreferenceResource::collection($preferences);
    }
}
