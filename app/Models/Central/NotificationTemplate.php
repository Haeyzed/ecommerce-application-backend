<?php

namespace App\Models\Central;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * Class NotificationTemplate
 *
 * Represents a customizable template for outgoing notification messages.
 *
 * @property int $id The unique identifier of the template.
 * @property string $event The event that triggers this template.
 * @property string $channel The channel this template targets (e.g., 'email', 'sms').
 * @property string|null $subject The subject line (typically used for emails).
 * @property string $body The main body content, often containing variable placeholders.
 * @property string|null $greeting Custom greeting line (e.g., 'Hello,').
 * @property string|null $closing Custom closing line (e.g., 'Best regards,').
 * @property string|null $sign_off Custom sign-off name (e.g., 'The Platform Team').
 * @property string|null $logo_url URL of the logo to display in the email header.
 * @property string|null $logo_alt Alt text for the logo image.
 * @property string|null $header_bg_color Background colour for the email header (hex).
 * @property string|null $accent_color Accent colour used for links and buttons (hex).
 * @property bool $is_active Indicates whether this template is currently active and used.
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
class NotificationTemplate extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'event',
        'channel',
        'subject',
        'body',
        'greeting',
        'closing',
        'sign_off',
        'logo_url',
        'logo_alt',
        'header_bg_color',
        'accent_color',
        'is_active',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'is_active' => 'bool',
        ];
    }
}
