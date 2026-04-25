<?php

namespace App\Models\Tenant\HR;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;

/**
 * Class JobPosting
 *
 * Represents an open job position advertised by the tenant.
 *
 * @property int $id The unique identifier of the job posting.
 * @property int|null $department_id The foreign key referencing the relevant department.
 * @property string $title The job title.
 * @property string $slug The URL-friendly slug of the job title.
 * @property string $description The detailed job description.
 * @property string $employment_type The type of employment (e.g., full_time).
 * @property string|null $location The location of the job.
 * @property bool $is_open Indicates whether the job posting is currently accepting applications.
 * @property Carbon|null $closes_at The timestamp when the posting automatically closes.
 * @property Carbon|null $created_at Timestamp of when the posting was created.
 * @property Carbon|null $updated_at Timestamp of when the posting was last updated.
 * @property-read Department|null $department The department this job posting belongs to.
 * @property-read Collection|Applicant[] $applicants The candidates who have applied to this posting.
 */
class JobPosting extends Model implements AuditableContract
{
    use Auditable, HasSlug;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'department_id',
        'title',
        'slug',
        'description',
        'employment_type',
        'location',
        'is_open',
        'closes_at',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'closes_at' => 'datetime',
            'is_open' => 'boolean',
        ];
    }

    /**
     * Get the options for generating the slug.
     */
    public function getSlugOptions(): SlugOptions
    {
        return SlugOptions::create()
            ->generateSlugsFrom('title')
            ->saveSlugsTo('slug');
    }

    /**
     * Get the department associated with the job posting.
     */
    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    /**
     * Get the applicants that applied to this job posting.
     */
    public function applicants(): HasMany
    {
        return $this->hasMany(Applicant::class);
    }
}
