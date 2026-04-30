<?php

namespace App\Http\Controllers\Tenant\Api\HR;

use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\Tenant\HR\StoreEmployeeDocumentRequest;
use App\Http\Resources\Tenant\HR\EmployeeDocumentResource;
use App\Models\Tenant\HR\EmployeeDocument;
use App\Services\Tenant\HR\EmployeeDocumentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Spatie\MediaLibrary\MediaCollections\Exceptions\FileDoesNotExist;
use Spatie\MediaLibrary\MediaCollections\Exceptions\FileIsTooBig;

/**
 * Employee Document Endpoints
 * * Handles uploading and managing official employee documents.
 */
class EmployeeDocumentController extends Controller
{
    public function __construct(
        private readonly EmployeeDocumentService $documentService
    ) {}

    /**
     * List all employee documents.
     */
    public function index(Request $request): JsonResponse
    {
        $filters = [
            'employee_id' => $request->integer('employee_id'),
            'expiring_within_days' => $request->integer('expiring_within_days'),
        ];

        $documents = $this->documentService->getPaginatedDocuments(
            array_filter($filters),
            $request->integer('per_page', 20)
        );

        return ApiResponse::success(
            data: EmployeeDocumentResource::collection($documents),
            message: 'Documents retrieved successfully',
            meta: ApiResponse::meta($documents)
        );
    }

    /**
     * Store a new employee document.
     *
     * @throws FileDoesNotExist
     * @throws FileIsTooBig
     */
    public function store(StoreEmployeeDocumentRequest $request): JsonResponse
    {
        $document = $this->documentService->createDocument($request->safe()->except(['file']));
        $document->addMedia($request->file('file'))->toMediaCollection('default');

        return ApiResponse::success(
            new EmployeeDocumentResource($document),
            'Document uploaded successfully',
            null,
            201
        );
    }

    /**
     * Delete an employee document.
     */
    public function destroy(int $id): JsonResponse
    {
        $document = EmployeeDocument::query()->findOrFail($id);
        $this->documentService->deleteDocument($document);

        return ApiResponse::success(null, 'Document deleted successfully');
    }
}
