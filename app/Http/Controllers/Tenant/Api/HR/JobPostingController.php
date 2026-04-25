<?php

namespace App\Http\Controllers\Tenant\Api\HR;

use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\Tenant\HR\StoreJobPostingRequest;
use App\Http\Resources\Tenant\HR\JobPostingResource;
use App\Models\Tenant\HR\JobPosting;
use App\Services\Tenant\HR\RecruitmentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Job Posting Endpoints
 * * Handles the creation and management of job advertisements.
 */
class JobPostingController extends Controller
{
    public function __construct(
        private readonly RecruitmentService $recruitmentService
    ) {}

    /**
     * List all job postings.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $perPage = $request->integer('per_page', 20);
        $postings = $this->recruitmentService->getPaginatedPostings($request->all(), $perPage);

        return ApiResponse::success(
            data: JobPostingResource::collection($postings),
            message: 'Job postings retrieved successfully',
            meta: ApiResponse::meta($postings)
        );
    }

    /**
     * Create a new job posting.
     *
     * @param StoreJobPostingRequest $request
     * @return JsonResponse
     */
    public function store(StoreJobPostingRequest $request): JsonResponse
    {
        $posting = $this->recruitmentService->createPosting($request->validated());

        return ApiResponse::success(
            new JobPostingResource($posting),
            'Job posting created successfully',
            null,
            201
        );
    }

    /**
     * Show a specific job posting.
     *
     * @param int $id
     * @return JsonResponse
     */
    public function show(int $id): JsonResponse
    {
        $posting = JobPosting::query()->findOrFail($id);

        return ApiResponse::success(
            new JobPostingResource($posting),
            'Job posting retrieved successfully'
        );
    }

    /**
     * Update an existing job posting.
     *
     * @param StoreJobPostingRequest $request
     * @param int $id
     * @return JsonResponse
     */
    public function update(StoreJobPostingRequest $request, int $id): JsonResponse
    {
        $posting = JobPosting::query()->findOrFail($id);
        $updatedPosting = $this->recruitmentService->updatePosting($posting, $request->validated());

        return ApiResponse::success(
            new JobPostingResource($updatedPosting),
            'Job posting updated successfully'
        );
    }

    /**
     * Delete a job posting.
     *
     * @param int $id
     * @return JsonResponse
     */
    public function destroy(int $id): JsonResponse
    {
        $posting = JobPosting::query()->findOrFail($id);
        $this->recruitmentService->deletePosting($posting);

        return ApiResponse::success(null, 'Job posting deleted successfully');
    }
}
