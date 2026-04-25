<?php

namespace App\Models\Central;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * Class AuditLog
 *
 * Represents a system-wide or tenant-specific audit log entry.
 *
 * @property int $id The unique identifier of the audit log.
 * @property string|null $tenant_id The ID of the tenant where the action occurred.
 * @property string|null $actor_type The morph class of the actor performing the action.
 * @property int|null $actor_id The ID of the actor performing the action.
 * @property string $action The description or key of the action performed.
 * @property string|null $subject_type The morph class of the entity acted upon.
 * @property int|null $subject_id The ID of the entity acted upon.
 * @property array|null $meta Additional context or metadata stored as JSON.
 * @property string|null $ip The IP address from which the action was performed.
 * @property Carbon|null $created_at Timestamp of when the log was recorded.
 * @property Carbon|null $updated_at Timestamp of when the log was last updated.
 */
class AuditLog extends Model
{
    /**
     * The database connection that should be used by the model.
     *
     * @var string
     */
    protected $connection = 'central';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'tenant_id',
        'actor_type',
        'actor_id',
        'action',
        'subject_type',
        'subject_id',
        'meta',
        'ip',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'meta' => 'array',
        ];
    }
}
