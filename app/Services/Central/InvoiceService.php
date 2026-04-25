<?php

namespace App\Services\Central;

use App\Models\Central\Invoice;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

/**
 * Class InvoiceService
 * * Handles business logic related to central platform invoices.
 */
class InvoiceService
{
    /**
     * Retrieve a paginated list of invoices.
     */
    public function getPaginatedInvoices(int $perPage = 20): LengthAwarePaginator
    {
        return Invoice::query()
            ->with('tenant')
            ->latest()
            ->paginate($perPage);
    }

    /**
     * Retrieve a specific invoice by its ID.
     */
    public function getInvoiceById(int $id): Invoice
    {
        return Invoice::query()
            ->with(['tenant', 'subscription'])
            ->findOrFail($id);
    }
}
