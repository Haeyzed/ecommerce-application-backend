<?php

namespace App\Models\Central;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Carbon;

/**
 * Class NotificationPreference
 *
 * Represents a user's or tenant's opt-in/opt-out choice for a specific notification event and channel.
 *
 * @property int $id The unique identifier of the preference.
 * @property string $notifiable_type The class name of the entity receiving the notification.
 * @property int|string $notifiable_id The ID of the entity receiving the notification.
 * @property string $event The specific event this preference applies to.
 * @property string $channel The notification channel (e.g., 'email', 'sms').
 * @property bool $enabled Indicates whether notifications for this event and channel are enabled.
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @property-read Model|\Eloquent $notifiable The entity this preference belongs to.
 */
class NotificationPreference extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'notifiable_type',
        'notifiable_id',
        'event',
        'channel',
        'enabled',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'enabled' => 'bool',
        ];
    }

    /**
     * Get the entity that this preference belongs to.
     *
     * @return MorphTo
     */
    public function notifiable(): MorphTo
    {
        return $this->morphTo();
    }
}
