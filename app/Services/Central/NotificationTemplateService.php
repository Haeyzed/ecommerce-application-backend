<?php

namespace App\Services\Central;

use App\Models\Central\NotificationTemplate;
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
     * @param  array  $filters  Query filters (e.g., event, channel).
     * @param  int  $perPage  Items per page.
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
     */
    public function createTemplate(array $data): NotificationTemplate
    {
        return NotificationTemplate::query()->create($data);
    }

    /**
     * Update an existing notification template.
     */
    public function updateTemplate(NotificationTemplate $template, array $data): NotificationTemplate
    {
        $template->update($data);

        return $template->fresh();
    }

    /**
     * Delete a notification template.
     */
    public function deleteTemplate(NotificationTemplate $template): void
    {
        $template->delete();
    }

    /**
     * Get the dictionary of available variables for each notification event.
     * This feeds the frontend UI so users know what placeholders they can use.
     */
    public function getAvailableVariables(): array
    {
        return [
            'tenant_registered' => [
                'tenant_name' => 'Name of the new Tenant',
                'domain' => 'Store Domain URL',
                'email' => 'Owner Login Email',
                'password' => 'Owner Login Password',
            ],
            'tenant_subscribed' => [
                'tenant_name' => 'Name of the Tenant',
                'plan_name' => 'Name of the subscribed plan',
            ],
            'tenant_canceled' => [
                'tenant_name' => 'Name of the Tenant',
                'plan_name' => 'Name of the canceled plan',
            ],
            'plan_expiring' => [
                'tenant_name' => 'Name of the Tenant',
                'plan_name' => 'Name of the expiring plan',
                'expiration_date' => 'Date of plan expiration',
            ],
            'password_reset' => [
                'name' => 'User Full Name',
                'url' => 'Password reset URL',
            ],
        ];
    }
}
