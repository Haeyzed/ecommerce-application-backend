<?php

namespace App\Services\Tenant\HR;

use App\Models\Tenant\HR\Employee;
use App\Models\Tenant\HR\Payslip;
use Carbon\CarbonInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

/**
 * Class PayrollService
 * * Handles business logic related to employee payroll and payslip generation.
 */
class PayrollService
{
    /**
     * Retrieve a paginated, filtered list of payslips.
     *
     * @param  array  $filters  Query filters (e.g., employee_id, status).
     * @param  int  $perPage  Items per page.
     */
    public function getPaginatedPayroll(array $filters = [], int $perPage = 20): LengthAwarePaginator
    {
        return Payslip::query()
            ->with('employee:id,first_name,last_name,employee_code')
            ->when($filters['employee_id'] ?? null, fn ($q, $v) => $q->where('employee_id', $v))
            ->when($filters['status'] ?? null, fn ($q, $v) => $q->where('status', $v))
            ->orderByDesc('period_end')
            ->paginate($perPage);
    }

    /**
     * Generate a new payslip for an employee.
     *
     * @param  Employee  $employee  The employee receiving the payslip.
     * @param  CarbonInterface  $start  The start date of the pay period.
     * @param  CarbonInterface  $end  The end date of the pay period.
     * @param  array  $deductions  A breakdown array of applied deductions.
     */
    public function generate(Employee $employee, CarbonInterface $start, CarbonInterface $end, array $deductions = []): Payslip
    {
        $gross = (int) $employee->salary_cents;
        $taxPct = (float) env('PAYROLL_TAX_PERCENT', 10);
        $tax = (int) round($gross * $taxPct / 100);
        $deduct = (int) array_sum(array_column($deductions, 'amount_cents'));
        $net = max(0, $gross - $tax - $deduct);

        return Payslip::query()->create([
            'employee_id' => $employee->id,
            'period_start' => $start,
            'period_end' => $end,
            'gross_cents' => $gross,
            'tax_cents' => $tax,
            'deductions_cents' => $deduct,
            'net_cents' => $net,
            'currency' => $employee->currency ?? 'USD',
            'breakdown' => ['deductions' => $deductions, 'tax_percent' => $taxPct],
            'status' => 'draft',
        ]);
    }

    /**
     * Mark a draft payslip as paid.
     */
    public function markPaid(Payslip $payslip): Payslip
    {
        $payslip->update(['status' => 'paid', 'paid_at' => now()]);

        return $payslip->fresh();
    }
}
