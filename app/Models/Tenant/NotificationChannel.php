<?php

namespace App\Models\Tenant;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * Class NotificationChannel
 *
 * Represents a system-wide notification channel configuration (e.g., email, SMS).
 *
 * @property int $id The unique identifier of the channel.
 * @property string $key The unique key for the channel (e.g., 'email', 'sms', 'push').
 * @property string $label The human-readable label for the channel.
 * @property bool $is_active Indicates if the channel is globally active.
 * @property array|null $config JSON configuration settings for the channel.
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
class NotificationChannel extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'key',
        'label',
        'is_active',
        'config',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'config' => 'array',
            'is_active' => 'bool',
        ];
    }
}
