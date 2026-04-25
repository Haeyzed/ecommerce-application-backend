<?php

namespace App\Services\Tenant\Notification;

use App\Models\Tenant\NotificationPreference;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Collection;
use Throwable;

/**
 * Class NotificationPreferenceService
 * * Handles business logic related to user notification preferences.
 */
class NotificationPreferenceService
{
    /**
     * Retrieve all notification preferences for a specific notifiable entity.
     *
     * @param Model $notifiable
     * @return Collection
     */
    public function getPreferencesFor(Model $notifiable): Collection
    {
        return NotificationPreference::query()
            ->where('notifiable_type', get_class($notifiable))
            ->where('notifiable_id', $notifiable->getKey())
            ->get();
    }

    /**
     * Bulk update or insert notification preferences for a notifiable entity.
     *
     * @param Model $notifiable
     * @param array $preferences
     * @return void
     * @throws Throwable
     */
    public function updatePreferencesFor(Model $notifiable, array $preferences): void
    {
        DB::transaction(function () use ($notifiable, $preferences) {
            foreach ($preferences as $pref) {
                NotificationPreference::query()->updateOrCreate(
                    [
                        'notifiable_type' => get_class($notifiable),
                        'notifiable_id'   => $notifiable->getKey(),
                        'event'           => $pref['event'],
                        'channel'         => $pref['channel'],
                    ],
                    ['enabled' => (bool) $pref['enabled']]
                );
            }
        });
    }

    /**
     * Check if a specific notification event and channel are enabled for a notifiable entity.
     *
     * @param Model $notifiable
     * @param string $event
     * @param string $channel
     * @param bool $default
     * @return bool
     */
    public function isEnabled(Model $notifiable, string $event, string $channel, bool $default = true): bool
    {
        $row = NotificationPreference::query()
            ->where('notifiable_type', get_class($notifiable))
            ->where('notifiable_id', $notifiable->getKey())
            ->where('event', $event)
            ->where('channel', $channel)
            ->first();

        return $row ? (bool) $row->enabled : $default;
    }
}
