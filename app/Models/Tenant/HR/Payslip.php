<?php

namespace App\Models\Tenant\HR;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;

/**
 * Class Payslip
 *
 * Represents an employee's salary record for a specific pay period.
 *
 * @property int $id The unique identifier of the payslip.
 * @property int $employee_id The foreign key referencing the employee.
 * @property Carbon $period_start The start date of the pay period.
 * @property Carbon $period_end The end date of the pay period.
 * @property int $gross_cents The gross pay in minor units (cents).
 * @property int $deductions_cents Total deductions in minor units.
 * @property int $tax_cents Total tax withheld in minor units.
 * @property int $net_cents The final net pay in minor units.
 * @property string $currency The ISO currency code for the payment.
 * @property array|null $breakdown Detailed JSON breakdown of allowances and deductions.
 * @property Carbon|null $paid_at Timestamp indicating when the payslip was disbursed.
 * @property string $status The current status of the payslip (e.g., draft, paid).
 * @property Carbon|null $created_at Timestamp of when the payslip was created.
 * @property Carbon|null $updated_at Timestamp of when the payslip was last updated.
 * @property-read Employee $employee The employee receiving the payslip.
 */
class Payslip extends Model implements AuditableContract
{
    use Auditable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'employee_id',
        'period_start',
        'period_end',
        'gross_cents',
        'deductions_cents',
        'tax_cents',
        'net_cents',
        'currency',
        'breakdown',
        'paid_at',
        'status',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'period_start' => 'date',
            'period_end' => 'date',
            'gross_cents' => 'integer',
            'deductions_cents' => 'integer',
            'tax_cents' => 'integer',
            'net_cents' => 'integer',
            'breakdown' => 'array',
            'paid_at' => 'datetime',
        ];
    }

    /**
     * Get the employee associated with the payslip.
     */
    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }
}
