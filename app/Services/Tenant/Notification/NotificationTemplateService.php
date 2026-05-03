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
            'customer_registered' => [
                'name' => 'Customer Full Name',
                'store_name' => 'Name of the Store',
            ],
            'admin_registered' => [
                'name' => 'Admin Full Name',
                'store_name' => 'Name of the Store',
                'email' => 'Admin Login Email',
                'password' => 'Generated Login Password',
            ],
            'password_reset' => [
                'name' => 'User Full Name',
                'token' => 'Secure Password Reset Token',
            ],
            'invoice_created' => [
                'name' => 'Customer Name',
                'invoice_id' => 'Unique Invoice Number',
                'amount' => 'Total Amount Due',
                'currency' => 'Currency Code (e.g., USD)',
                'due_date' => 'Date the payment is due',
            ],
            'invoice_paid' => [
                'name' => 'Customer Name',
                'invoice_id' => 'Unique Invoice Number',
                'amount' => 'Total Amount Paid',
                'currency' => 'Currency Code',
            ],
            'leave_approved' => [
                'name' => 'Employee Name',
                'start_date' => 'Leave Start Date',
                'end_date' => 'Leave End Date',
                'approver_name' => 'Name of the Manager/HR who approved',
            ],
            'leave_rejected' => [
                'name' => 'Employee Name',
                'reason' => 'Reason for rejection',
            ],
            'payslip_generated' => [
                'name' => 'Employee Name',
                'period_start' => 'Start of the pay period',
                'period_end' => 'End of the pay period',
                'net_amount' => 'Total net pay received',
                'currency' => 'Currency Code',
            ],
            'interview_scheduled' => [
                'name' => 'Applicant Name',
                'job_title' => 'Title of the Job',
                'scheduled_at' => 'Date and Time of Interview',
                'mode' => 'Interview Mode (e.g., Video, Onsite)',
            ],
        ];
    }
}
