<?php

namespace App\Http\Controllers\Central\Api;

use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Services\Central\AuditLogService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Audit Log Endpoints
 * Handles the retrieval of central system audit logs.
 */
class AuditLogController extends Controller
{
    /**
     * Create a new AuditLogController instance.
     */
    public function __construct(
        private readonly AuditLogService $auditLogService
    ) {}

    /**
     * List all audit logs.
     */
    public function index(Request $request): JsonResponse
    {
        $logs = $this->auditLogService->getPaginatedLogs(
            $request->query('tenant_id'),
            $request->query('action')
        );

        return ApiResponse::success(
            ['audit_logs' => $logs],
            'Audit logs retrieved successfully'
        );
    }
}
