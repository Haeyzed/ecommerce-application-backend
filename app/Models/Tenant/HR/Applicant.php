<?php

namespace App\Models\Tenant\HR;

use App\Traits\Auditable;
use App\Traits\HasTenantMedia;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;
use Spatie\MediaLibrary\HasMedia;

/**
 * Class Applicant
 *
 * Represents a candidate applying for a specific job posting.
 *
 * @property int $id The unique identifier of the applicant.
 * @property int $job_posting_id The foreign key referencing the associated job posting.
 * @property string $first_name The applicant's first name.
 * @property string $last_name The applicant's last name.
 * @property string $email The applicant's email address.
 * @property string|null $phone The applicant's phone number.
 * @property string|null $resume_path The file path or URL to the applicant's resume.
 * @property string $status The current status of the application (e.g., applied, interviewing, hired).
 * @property string|null $cover_letter The applicant's cover letter or notes.
 * @property Carbon|null $created_at Timestamp of when the application was submitted.
 * @property Carbon|null $updated_at Timestamp of when the application was last updated.
 * @property-read JobPosting $jobPosting The job posting this applicant applied for.
 * @property-read Collection|Interview[] $interviews The interviews scheduled for this applicant.
 */
class Applicant extends Model implements AuditableContract, HasMedia
{
    use Auditable, HasTenantMedia;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'job_posting_id',
        'first_name',
        'last_name',
        'email',
        'phone',
        'resume_path',
        'status',
        'cover_letter',
    ];

    /**
     * Get the job posting the applicant applied to.
     */
    public function jobPosting(): BelongsTo
    {
        return $this->belongsTo(JobPosting::class);
    }

    /**
     * Get the interviews associated with the applicant.
     */
    public function interviews(): HasMany
    {
        return $this->hasMany(Interview::class);
    }
}
