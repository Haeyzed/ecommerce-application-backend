<?php

namespace App\Models\Tenant\HR;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;

/**
 * Class Interview
 *
 * Represents an interview session between an applicant and an employee (interviewer).
 *
 * @property int $id The unique identifier of the interview.
 * @property int $applicant_id The foreign key referencing the applicant.
 * @property int|null $interviewer_employee_id The foreign key referencing the employee conducting the interview.
 * @property Carbon $scheduled_at The timestamp when the interview is scheduled.
 * @property string $mode The mode of the interview (e.g., onsite, video, phone).
 * @property string $status The status of the interview (e.g., scheduled, completed).
 * @property float|null $score The score given by the interviewer.
 * @property string|null $notes Additional notes from the interviewer.
 * @property Carbon|null $created_at Timestamp of when the interview was created.
 * @property Carbon|null $updated_at Timestamp of when the interview was last updated.
 * @property-read Applicant $applicant The applicant being interviewed.
 * @property-read Employee|null $interviewer The employee conducting the interview.
 */
class Interview extends Model implements AuditableContract
{
    use Auditable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'applicant_id',
        'interviewer_employee_id',
        'scheduled_at',
        'mode',
        'status',
        'score',
        'notes',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'scheduled_at' => 'datetime',
            'score' => 'decimal:2',
        ];
    }

    /**
     * Get the applicant for this interview.
     */
    public function applicant(): BelongsTo
    {
        return $this->belongsTo(Applicant::class);
    }

    /**
     * Get the employee conducting the interview.
     */
    public function interviewer(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'interviewer_employee_id');
    }
}
