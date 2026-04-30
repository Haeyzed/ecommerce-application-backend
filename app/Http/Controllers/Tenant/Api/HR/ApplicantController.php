<?php

namespace App\Http\Controllers\Tenant\Api\HR;

use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\Tenant\HR\StoreApplicantRequest;
use App\Http\Resources\Tenant\HR\ApplicantResource;
use App\Models\Tenant\HR\Applicant;
use App\Services\Tenant\HR\RecruitmentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Applicant Endpoints
 * * Handles candidate submissions and tracking through the recruitment pipeline.
 */
class ApplicantController extends Controller
{
    public function __construct(
        private readonly RecruitmentService $recruitmentService
    ) {}

    /**
     * List all applicants.
     */
    public function index(Request $request): JsonResponse
    {
        $perPage = $request->integer('per_page', 20);

        $filters = [
            'search' => $request->string('search'),
            'job_posting_id' => $request->integer('job_posting_id'),
            'status' => $request->string('status'),
        ];

        $applicants = $this->recruitmentService->getPaginatedApplicants($filters, $perPage);

        return ApiResponse::success(
            data: ApplicantResource::collection($applicants),
            message: 'Applicants retrieved successfully',
            meta: ApiResponse::meta($applicants)
        );
    }

    /**
     * Submit a new application.
     */
    public function store(StoreApplicantRequest $request): JsonResponse
    {
        $data = $request->safe()->except(['resume']);

        if ($request->hasFile('resume')) {
            $data['resume_path'] = $request->file('resume')->store('resumes', 's3');
        }

        $applicant = $this->recruitmentService->createApplicant($data);

        return ApiResponse::success(
            new ApplicantResource($applicant),
            'Applicant created successfully',
            null,
            201
        );
    }

    /**
     * Move an applicant to a different stage in the recruitment process.
     */
    public function move(Request $request, int $id): JsonResponse
    {
        $request->validate(['status' => 'required|in:applied,screening,interview,offer,hired,rejected']);

        $applicant = Applicant::query()->findOrFail($id);
        $updatedApplicant = $this->recruitmentService->updateApplicantStatus($applicant, $request->input('status'));

        return ApiResponse::success(
            new ApplicantResource($updatedApplicant),
            'Applicant status updated successfully'
        );
    }
}
