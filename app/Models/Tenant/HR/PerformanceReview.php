<?php

namespace App\Models\Tenant\HR;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;

/**
 * Class PerformanceReview
 *
 * Represents an evaluation of an employee's performance over a specific period.
 *
 * @property int $id The unique identifier of the performance review.
 * @property int $employee_id The foreign key referencing the employee being reviewed.
 * @property int|null $reviewer_employee_id The foreign key referencing the employee conducting the review.
 * @property Carbon $period_start The start date of the evaluation period.
 * @property Carbon $period_end The end date of the evaluation period.
 * @property float|null $rating The overall performance rating/score.
 * @property array|null $criteria A JSON array containing scores for specific performance criteria.
 * @property string|null $comments Additional notes or feedback from the reviewer.
 * @property string $status The current status of the review (e.g., draft, completed).
 * @property Carbon|null $created_at Timestamp of when the review was created.
 * @property Carbon|null $updated_at Timestamp of when the review was last updated.
 * @property-read Employee $employee The employee being evaluated.
 * @property-read Employee|null $reviewer The employee (usually a manager) conducting the evaluation.
 */
class PerformanceReview extends Model implements AuditableContract
{
    use Auditable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'employee_id',
        'reviewer_employee_id',
        'period_start',
        'period_end',
        'rating',
        'criteria',
        'comments',
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
            'rating' => 'decimal:2',
            'criteria' => 'array',
        ];
    }

    /**
     * Get the employee who is the subject of the review.
     */
    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    /**
     * Get the employee conducting the review.
     */
    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'reviewer_employee_id');
    }
}
