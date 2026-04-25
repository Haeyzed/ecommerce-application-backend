<?php

namespace App\Http\Controllers\Tenant\Api\HR;

use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\Tenant\HR\StoreLeaveRequest;
use App\Http\Resources\Tenant\HR\LeaveRequestResource;
use App\Models\Tenant\HR\Employee;
use App\Models\Tenant\HR\LeaveRequest as LeaveRequestModel;
use App\Services\Tenant\HR\LeaveApprovalService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Leave Endpoints
 * * Handles employee leave requests, approvals, and rejections.
 */
class LeaveController extends Controller
{
    public function __construct(
        private readonly LeaveApprovalService $leaveApprovalService
    ) {}

    /**
     * List all leave requests.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $perPage = $request->integer('per_page', 20);
        $leaves = $this->leaveApprovalService->listPaginated($request->all(), $perPage);

        return ApiResponse::success(
            data: LeaveRequestResource::collection($leaves),
            message: 'Leave requests retrieved successfully',
            meta: ApiResponse::meta($leaves)
        );
    }

    /**
     * Submit a new leave request.
     *
     * @param StoreLeaveRequest $request
     * @return JsonResponse
     */
    public function store(StoreLeaveRequest $request): JsonResponse
    {
        $employee = Employee::query()->findOrFail($request->integer('employee_id'));
        $leave = $this->leaveApprovalService->request($employee, $request->validated());

        return ApiResponse::success(
            new LeaveRequestResource($leave),
            'Leave request submitted successfully',
            null,
            201
        );
    }

    /**
     * Approve a leave request.
     *
     * @param Request $request
     * @param int $id The ID of the leave request.
     * @return JsonResponse
     */
    public function approve(Request $request, int $id): JsonResponse
    {
        $request->validate(['approver_employee_id' => 'required|integer|exists:employees,id']);

        $leave = LeaveRequestModel::query()->findOrFail($id);
        $approver = Employee::query()->findOrFail($request->integer('approver_employee_id'));

        $approvedLeave = $this->leaveApprovalService->approve($leave, $approver);

        return ApiResponse::success(
            new LeaveRequestResource($approvedLeave),
            'Leave request approved successfully'
        );
    }

    /**
     * Reject a leave request.
     *
     * @param Request $request
     * @param int $id The ID of the leave request.
     * @return JsonResponse
     */
    public function reject(Request $request, int $id): JsonResponse
    {
        $request->validate([
            'approver_employee_id' => 'required|integer|exists:employees,id',
            'reason' => 'nullable|string'
        ]);

        $leave = LeaveRequestModel::query()->findOrFail($id);
        $approver = Employee::query()->findOrFail($request->integer('approver_employee_id'));

        $rejectedLeave = $this->leaveApprovalService->reject($leave, $approver, $request->input('reason'));

        return ApiResponse::success(
            new LeaveRequestResource($rejectedLeave),
            'Leave request rejected successfully'
        );
    }
}
