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
     * @param  array  $filters  Query filters (e.g., employee_id, expiring_within_days).
     * @param  int  $perPage  Items per page.
     */
    public function getPaginatedDocuments(array $filters = [], int $perPage = 20): LengthAwarePaginator
    {
        return EmployeeDocument::query()
            ->filter($filters)
            ->orderByDesc('id')
            ->paginate($perPage);
    }

    /**
     * Create an employee document record.
     */
    public function createDocument(array $data): EmployeeDocument
    {
        return EmployeeDocument::query()->create($data);
    }

    /**
     * Delete an employee document.
     */
    public function deleteDocument(EmployeeDocument $document): void
    {
        $document->delete();
    }
}
