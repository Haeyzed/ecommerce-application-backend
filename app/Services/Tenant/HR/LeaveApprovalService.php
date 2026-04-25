<?php

namespace App\Services\Tenant\HR;

use App\Models\Tenant\HR\Employee;
use App\Models\Tenant\HR\LeaveRequest;
use Carbon\Carbon;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

/**
 * Class LeaveApprovalService
 * * Handles business logic related to employee leave requests and approvals.
 */
class LeaveApprovalService
{
    /**
     * Retrieve a paginated, filtered list of leave requests.
     *
     * @param array $filters Query filters (e.g., employee_id, status).
     * @param int $perPage Items per page.
     * @return LengthAwarePaginator
     */
    public function listPaginated(array $filters = [], int $perPage = 20): LengthAwarePaginator
    {
        return LeaveRequest::query()
            ->with('employee:id,first_name,last_name,employee_code')
            ->when($filters['employee_id'] ?? null, fn ($q, $v) => $q->where('employee_id', $v))
            ->when($filters['status'] ?? null, fn ($q, $v) => $q->where('status', $v))
            ->orderByDesc('id')
            ->paginate($perPage);
    }

    /**
     * Submit a new leave request for an employee.
     *
     * @param Employee $employee The employee requesting leave.
     * @param array $data Validated leave request data.
     * @return LeaveRequest
     */
    public function request(Employee $employee, array $data): LeaveRequest
    {
        $start = Carbon::parse($data['start_date']);
        $end   = Carbon::parse($data['end_date']);
        $days  = max(1, $end->diffInDays($start) + 1);

        return LeaveRequest::query()->create([
            'employee_id' => $employee->id,
            'type'        => $data['type'],
            'start_date'  => $start,
            'end_date'    => $end,
            'days'        => $days,
            'reason'      => $data['reason'] ?? null,
            'status'      => 'pending',
        ]);
    }

    /**
     * Approve a pending leave request.
     *
     * @param LeaveRequest $req
     * @param Employee $approver The employee approving the request.
     * @return LeaveRequest
     */
    public function approve(LeaveRequest $req, Employee $approver): LeaveRequest
    {
        $req->update([
            'status' => 'approved',
            'approved_by_employee_id' => $approver->id
        ]);

        return $req->fresh();
    }

    /**
     * Reject a pending leave request.
     *
     * @param LeaveRequest $req
     * @param Employee $approver The employee rejecting the request.
     * @param string|null $reason Optional rejection reason.
     * @return LeaveRequest
     */
    public function reject(LeaveRequest $req, Employee $approver, ?string $reason = null): LeaveRequest
    {
        $req->update([
            'status' => 'rejected',
            'approved_by_employee_id' => $approver->id,
            'reason' => $reason ?? $req->reason
        ]);

        return $req->fresh();
    }
}
