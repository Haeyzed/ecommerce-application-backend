<?php

namespace App\Http\Controllers\Tenant\Api\HR;

use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\Tenant\HR\ScheduleInterviewRequest;
use App\Http\Resources\Tenant\HR\InterviewResource;
use App\Models\Tenant\HR\Applicant;
use App\Models\Tenant\HR\Employee;
use App\Services\Tenant\HR\RecruitmentService;
use Illuminate\Http\JsonResponse;

/**
 * Interview Endpoints
 * * Handles scheduling and managing applicant interviews.
 */
class InterviewController extends Controller
{
    public function __construct(
        private readonly RecruitmentService $recruitmentService
    ) {}

    /**
     * Schedule an interview for an applicant.
     *
     * @param ScheduleInterviewRequest $request
     * @return JsonResponse
     */
    public function schedule(ScheduleInterviewRequest $request): JsonResponse
    {
        $applicant = Applicant::query()->findOrFail($request->integer('applicant_id'));

        $interviewer = $request->filled('interviewer_employee_id')
            ? Employee::query()->find($request->integer('interviewer_employee_id'))
            : null;

        $interview = $this->recruitmentService->scheduleInterview($applicant, $interviewer, $request->validated());

        return ApiResponse::success(
            new InterviewResource($interview),
            'Interview scheduled successfully',
            null,
            201
        );
    }
}
