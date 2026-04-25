<?php

namespace App\Services\Tenant\Notification;

use App\Models\Tenant\NotificationTemplate;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

/**
 * Class NotificationTemplateService
 * * Handles business logic related to customizable notification templates.
 */
class NotificationTemplateService
{
    /**
     * Retrieve a paginated, filtered list of notification templates.
     *
     * @param array $filters Query filters (e.g., event, channel).
     * @param int $perPage Items per page.
     * @return LengthAwarePaginator
     */
    public function getPaginatedTemplates(array $filters = [], int $perPage = 30): LengthAwarePaginator
    {
        return NotificationTemplate::query()
            ->when($filters['event'] ?? null, fn ($q, $v) => $q->where('event', $v))
            ->when($filters['channel'] ?? null, fn ($q, $v) => $q->where('channel', $v))
            ->orderBy('event')
            ->paginate($perPage);
    }

    /**
     * Create a new notification template.
     *
     * @param array $data
     * @return NotificationTemplate
     */
    public function createTemplate(array $data): NotificationTemplate
    {
        return NotificationTemplate::query()->create($data);
    }

    /**
     * Update an existing notification template.
     *
     * @param NotificationTemplate $template
     * @param array $data
     * @return NotificationTemplate
     */
    public function updateTemplate(NotificationTemplate $template, array $data): NotificationTemplate
    {
        $template->update($data);
        return $template->fresh();
    }

    /**
     * Delete a notification template.
     *
     * @param NotificationTemplate $template
     * @return void
     */
    public function deleteTemplate(NotificationTemplate $template): void
    {
        $template->delete();
    }
}
