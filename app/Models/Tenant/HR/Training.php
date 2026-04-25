<?php

namespace App\Models\Tenant\HR;

use App\Traits\Auditable;
use App\Traits\HasTenantMedia;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Carbon;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;
use Spatie\MediaLibrary\HasMedia;

/**
 * Class Training
 *
 * Represents a training course, workshop, or program available to employees.
 *
 * @property int $id The unique identifier of the training.
 * @property string $title The title of the training program.
 * @property string|null $description A detailed description of the training.
 * @property Carbon|null $starts_at Timestamp of when the training begins.
 * @property Carbon|null $ends_at Timestamp of when the training concludes.
 * @property string|null $location The physical or virtual location of the training.
 * @property bool $is_mandatory Indicates whether the training is required for assigned employees.
 * @property Carbon|null $created_at Timestamp of when the training was created.
 * @property Carbon|null $updated_at Timestamp of when the training was last updated.
 * @property-read Collection|Employee[] $employees The employees enrolled in this training.
 */
class Training extends Model implements AuditableContract, HasMedia
{
    use Auditable, HasTenantMedia;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'title',
        'description',
        'starts_at',
        'ends_at',
        'location',
        'is_mandatory',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'starts_at' => 'datetime',
            'ends_at' => 'datetime',
            'is_mandatory' => 'boolean',
        ];
    }

    /**
     * Get the employees enrolled in the training.
     */
    public function employees(): BelongsToMany
    {
        return $this->belongsToMany(Employee::class, 'employee_training')
            ->withPivot('status', 'completed_at')
            ->withTimestamps();
    }
}
