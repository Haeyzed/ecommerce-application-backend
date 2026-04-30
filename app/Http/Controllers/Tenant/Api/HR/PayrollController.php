<?php

namespace App\Http\Controllers\Tenant\Api\HR;

use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\Tenant\HR\GeneratePayslipRequest;
use App\Http\Resources\Tenant\HR\PayslipResource;
use App\Models\Tenant\HR\Employee;
use App\Models\Tenant\HR\Payslip;
use App\Services\Tenant\HR\PayrollService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Payroll Endpoints
 * * Handles management and generation of employee payslips.
 */
class PayrollController extends Controller
{
    public function __construct(
        private readonly PayrollService $payrollService
    ) {}

    /**
     * List all payslips.
     */
    public function index(Request $request): JsonResponse
    {
        $perPage = $request->integer('per_page', 20);
        $payslips = $this->payrollService->listPaginated($request->all(), $perPage);

        return ApiResponse::success(
            data: PayslipResource::collection($payslips),
            message: 'Payslips retrieved successfully',
            meta: ApiResponse::meta($payslips)
        );
    }

    /**
     * Generate a new payslip.
     */
    public function generate(GeneratePayslipRequest $request): JsonResponse
    {
        $employee = Employee::query()->findOrFail($request->integer('employee_id'));

        $slip = $this->payrollService->generate(
            $employee,
            Carbon::parse($request->input('period_start')),
            Carbon::parse($request->input('period_end')),
            $request->input('deductions', [])
        );

        return ApiResponse::success(
            new PayslipResource($slip),
            'Payslip generated successfully',
            null,
            201
        );
    }

    /**
     * Mark a payslip as paid.
     */
    public function markPaid(int $id): JsonResponse
    {
        $payslip = Payslip::query()->findOrFail($id);
        $updatedPayslip = $this->payrollService->markPaid($payslip);

        return ApiResponse::success(
            new PayslipResource($updatedPayslip),
            'Payslip marked as paid successfully'
        );
    }
}
