<?php

namespace App\Models\Tenant\HR;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;

/**
 * Class Position
 *
 * Represents a specific job role or title within a department.
 *
 * @property int $id The unique identifier of the position.
 * @property int $department_id The foreign key referencing the department this position belongs to.
 * @property string $title The job title (e.g., "Senior Software Engineer").
 * @property int|null $min_salary_cents The minimum salary band for this position in minor units.
 * @property int|null $max_salary_cents The maximum salary band for this position in minor units.
 * @property Carbon|null $created_at Timestamp of when the position was created.
 * @property Carbon|null $updated_at Timestamp of when the position was last updated.
 * @property-read Department $department The department this position falls under.
 */
class Position extends Model implements AuditableContract
{
    use Auditable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'department_id',
        'title',
        'min_salary_cents',
        'max_salary_cents',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'min_salary_cents' => 'integer',
            'max_salary_cents' => 'integer',
        ];
    }

    /**
     * Get the department that this position belongs to.
     */
    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }
}
