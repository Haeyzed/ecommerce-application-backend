<?php

namespace App\Http\Controllers\Central\Api;

use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Services\Central\InvoiceService;
use Illuminate\Http\JsonResponse;

/**
 * Invoice Endpoints
 * Handles the retrieval of billing invoices on the central platform.
 */
class InvoiceController extends Controller
{
    /**
     * Create a new InvoiceController instance.
     */
    public function __construct(
        private readonly InvoiceService $invoiceService
    ) {
        $this->middleware('permission:view invoices')->only(['index', 'show']);
    }

    /**
     * List all invoices.
     */
    public function index(): JsonResponse
    {
        $invoices = $this->invoiceService->getPaginatedInvoices();

        return ApiResponse::success(
            ['invoices' => $invoices],
            'Invoices retrieved successfully'
        );
    }

    /**
     * Get details of a specific invoice.
     */
    public function show(int $id): JsonResponse
    {
        $invoice = $this->invoiceService->getInvoiceById($id);

        return ApiResponse::success(
            ['invoice' => $invoice],
            'Invoice retrieved successfully'
        );
    }
}
