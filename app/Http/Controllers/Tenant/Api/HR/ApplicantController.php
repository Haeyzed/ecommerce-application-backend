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
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $perPage = $request->integer('per_page', 20);
        $applicants = $this->recruitmentService->getPaginatedApplicants($request->all(), $perPage);

        return ApiResponse::success(
            data: ApplicantResource::collection($applicants),
            message: 'Applicants retrieved successfully',
            meta: ApiResponse::meta($applicants)
        );
    }

    /**
     * Submit a new application.
     *
     * @param StoreApplicantRequest $request
     * @return JsonResponse
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
     *
     * @param Request $request
     * @param int $id
     * @return JsonResponse
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
