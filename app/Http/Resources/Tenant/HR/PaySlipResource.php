<?php

namespace App\Http\Resources\Tenant\HR;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PayslipResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            /**
             * The unique identifier for the payslip.
             *
             * @var int $id
             *
             * @example 110
             */
            'id' => $this->id,

            /**
             * The ID of the employee receiving the payslip.
             *
             * @var int $employee_id
             *
             * @example 42
             */
            'employee_id' => $this->employee_id,

            /**
             * The start date of the pay period.
             *
             * @var string $period_start
             *
             * @example "2026-04-01"
             */
            'period_start' => $this->period_start,

            /**
             * The end date of the pay period.
             *
             * @var string $period_end
             *
             * @example "2026-04-30"
             */
            'period_end' => $this->period_end,

            /**
             * The gross pay amount in minor units (cents).
             *
             * @var int $gross_cents
             *
             * @example 750000
             */
            'gross_cents' => $this->gross_cents,

            /**
             * The calculated tax amount in minor units (cents).
             *
             * @var int $tax_cents
             *
             * @example 75000
             */
            'tax_cents' => $this->tax_cents,

            /**
             * The total amount of all deductions in minor units (cents).
             *
             * @var int $deductions_cents
             *
             * @example 5000
             */
            'deductions_cents' => $this->deductions_cents,

            /**
             * The final net pay amount in minor units (cents).
             *
             * @var int $net_cents
             *
             * @example 670000
             */
            'net_cents' => $this->net_cents,

            /**
             * The ISO currency code for the payment.
             *
             * @var string $currency
             *
             * @example "USD"
             */
            'currency' => $this->currency,

            /**
             * The current status of the payslip (e.g., draft, paid).
             *
             * @var string $status
             *
             * @example "paid"
             */
            'status' => $this->status,

            /**
             * The date and time the payslip was marked as paid.
             *
             * @var string|null $paid_at
             *
             * @example "2026-05-01 09:00:00"
             */
            'paid_at' => $this->paid_at,

            /**
             * A JSON breakdown object containing specific deduction labels and details.
             *
             * @var array|null $breakdown
             *
             * @example {"tax_percent": 10, "deductions": [{"label": "Insurance", "amount_cents": 5000}]}
             */
            'breakdown' => $this->breakdown,
        ];
    }
}
