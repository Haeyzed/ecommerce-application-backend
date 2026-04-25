<?php

namespace App\Services\Central;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;

/**
 * Class AuditLogService
 * * Handles business logic related to central system audit logs.
 */
class AuditLogService
{
    /**
     * Retrieve a paginated and filtered list of audit logs.
     */
    public function getPaginatedLogs(?string $tenantId = null, ?string $action = null, int $perPage = 50): LengthAwarePaginator
    {
        return AuditLog::query()
            ->when($tenantId, fn ($q, $v) => $q->where('tenant_id', $v))
            ->when($action, fn ($q, $v) => $q->where('action', $v))
            ->latest()
            ->paginate($perPage);
    }

    /**
     * Create a new audit log entry.
     *
     * @param  string  $action  The action being logged.
     * @param  array  $context  Additional context data.
     */
    public function log(string $action, array $context = []): AuditLog
    {
        return AuditLog::query()->create(array_merge([
            'action' => $action,
            'ip' => request()->ip(),
        ], $context));
    }
}
