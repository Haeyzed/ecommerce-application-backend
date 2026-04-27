<?php

namespace App\Notifications\Tenant;

use App\Models\Tenant\NotificationPreference;
use App\Models\Tenant\NotificationTemplate;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\HtmlString;

/**
 * Class DynamicTemplateNotification
 * Fetches an email template from the database, parses the {variables}, and sends it.
 */
class DynamicTemplateNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * @param string $event The event name (e.g., 'staff_registered', 'leave_approved')
     * @param array $templateData The key-value pairs to swap into the template
     */
    public function __construct(
        public readonly string $event,
        public readonly array $templateData = []
    ) {}

    /**
     * Get the notification's delivery channels.
     *
     * @param mixed $notifiable
     * @return array<int, string>
     */
    public function via(mixed $notifiable): array
    {
        $channels = [];

        // Check if the user has opted out of email notifications for this event
        if ($this->shouldSendViaChannel($notifiable, 'email')) {
            $channels[] = 'mail';
        }

        return $channels;
    }

    /**
     * Build the mail representation of the notification.
     *
     * @param mixed $notifiable
     * @return MailMessage
     */
    public function toMail(mixed $notifiable): MailMessage
    {
        $template = NotificationTemplate::query()
            ->where('event', $this->event)
            ->where('channel', 'email')
            ->where('is_active', true)
            ->first();

        // Fallback if template is missing or disabled
        if (!$template) {
            return (new MailMessage)
                ->subject("Notification: {$this->event}")
                ->line("You have a new notification regarding: {$this->event}.");
        }

        // Parse the {variables} in the subject and body
        $subject = $this->parseVariables($template->subject ?? 'New Notification', $this->templateData);
        $body = $this->parseVariables($template->body, $this->templateData);

        // Convert newlines to <br> tags so the email formats correctly
        $formattedBody = nl2br(e($body));

        return (new MailMessage)
            ->subject($subject)
            ->line(new HtmlString($formattedBody));
    }

    /**
     * Swap {keys} with actual values.
     */
    private function parseVariables(string $text, array $data): string
    {
        $replacements = [];
        foreach ($data as $key => $value) {
            $replacements['{' . $key . '}'] = $value;
        }

        return strtr($text, $replacements);
    }

    /**
     * Check if the notification should be sent via the specified channel.
     *
     * @param mixed $notifiable
     * @param string $channel
     * @return bool
     */
    private function shouldSendViaChannel(mixed $notifiable, string $channel): bool
    {
        if (! $notifiable instanceof Model) {
            return true; // Default to sending if not a tracked entity (e.g. anonymous route)
        }

        $preference = NotificationPreference::query()
            ->where('notifiable_type', get_class($notifiable))
            ->where('notifiable_id', $notifiable->getKey())
            ->where('event', $this->event)
            ->where('channel', $channel)
            ->first();

        return !$preference || (bool)$preference->enabled;
    }
}
