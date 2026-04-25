<?php

namespace App\Services\Tenant\HR;

use App\Models\Tenant\HR\EmployeeDocument;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

/**
 * Class EmployeeDocumentService
 * * Handles business logic related to tenant employee documents.
 */
class EmployeeDocumentService
{
    /**
     * Retrieve a paginated, filtered list of employee documents.
     *
     * @param array $filters Query filters (e.g., employee_id, expiring_within_days).
     * @param int $perPage Items per page.
     * @return LengthAwarePaginator
     */
    public function getPaginatedDocuments(array $filters = [], int $perPage = 20): LengthAwarePaginator
    {
        return EmployeeDocument::query()
            ->when($filters['employee_id'] ?? null, fn ($q, $v) => $q->where('employee_id', $v))
            ->when($filters['expiring_within_days'] ?? null, fn ($q, $v) => $q->whereNotNull('expires_at')->whereDate('expires_at', '<=', now()->addDays((int) $v)))
            ->orderByDesc('id')
            ->paginate($perPage);
    }

    /**
     * Create an employee document record.
     *
     * @param array $data
     * @return EmployeeDocument
     */
    public function createDocument(array $data): EmployeeDocument
    {
        return EmployeeDocument::query()->create($data);
    }

    /**
     * Delete an employee document.
     *
     * @param EmployeeDocument $document
     * @return void
     */
    public function deleteDocument(EmployeeDocument $document): void
    {
        $document->delete();
    }
}
