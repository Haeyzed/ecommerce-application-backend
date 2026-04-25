<?php

namespace App\Models\Tenant\HR;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;

/**
 * Class Goal
 *
 * Represents a performance or developmental objective assigned to an employee.
 *
 * @property int $id The unique identifier of the goal.
 * @property int $employee_id The foreign key referencing the employee.
 * @property string $title The title of the goal.
 * @property string|null $description The detailed description of the goal.
 * @property Carbon|null $target_date The deadline or target date for completion.
 * @property int $progress_percent The current completion progress as a percentage.
 * @property string $status The current status of the goal (e.g., open, completed).
 * @property Carbon|null $created_at Timestamp of when the goal was created.
 * @property Carbon|null $updated_at Timestamp of when the goal was last updated.
 * @property-read Employee $employee The employee this goal is assigned to.
 */
class Goal extends Model implements AuditableContract
{
    use Auditable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'employee_id',
        'title',
        'description',
        'target_date',
        'progress_percent',
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
            'target_date' => 'date',
            'progress_percent' => 'integer',
        ];
    }

    /**
     * Get the employee the goal belongs to.
     */
    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }
}
