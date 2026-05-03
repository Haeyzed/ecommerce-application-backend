<?php

namespace App\Notifications\Tenant;

use App\Models\Tenant\NotificationPreference;
use App\Models\Tenant\NotificationTemplate;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

/**
 * Class DynamicTemplateNotification
 * Fetches an email template from the database, parses the {variables}, and sends it.
 */
class DynamicTemplateNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * @param  string  $event  The event name (e.g., 'admin_registered', 'leave_approved')
     * @param  array  $templateData  The key-value pairs to swap into the template
     */
    public function __construct(
        public readonly string $event,
        public readonly array $templateData = []
    ) {}

    /**
     * Get the notification's delivery channels.
     *
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
     * Uses the custom `emails.notification` Blade view instead of Laravel's
     * default markdown layout so the received email matches the exact design
     * defined in the project's HTML template.
     */
    public function toMail(mixed $notifiable): MailMessage
    {
        $template = NotificationTemplate::query()
            ->where('event', $this->event)
            ->where('channel', 'email')
            ->where('is_active', true)
            ->first();

        // Fallback if template is missing or disabled
        if (! $template) {
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
            ->view('emails.notification', [
                'body' => $formattedBody,
                'subject' => $subject,
                'greeting' => $template->greeting ?? $this->templateData['greeting'] ?? 'Hello,',
                'closing' => $template->closing ?? $this->templateData['closing'] ?? 'Best regards,',
                'signOff' => $template->sign_off ?? $this->templateData['sign_off'] ?? config('app.name'),
                'logoUrl' => $template->logo_url ?? $this->templateData['logo_url'] ?? null,
                'logoAlt' => $template->logo_alt ?? $this->templateData['logo_alt'] ?? 'Logo',
                'headerBgColor' => $template->header_bg_color ?? $this->templateData['header_bg_color'] ?? '#1e2b2e',
                'accentColor' => $template->accent_color ?? $this->templateData['accent_color'] ?? '#73bc1c',
            ]);
    }

    /**
     * Swap {keys} with actual values.
     */
    private function parseVariables(string $text, array $data): string
    {
        $replacements = [];
        foreach ($data as $key => $value) {
            $replacements['{'.$key.'}'] = $value;
        }

        return strtr($text, $replacements);
    }

    /**
     * Check if the notification should be sent via the specified channel.
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

        return ! $preference || (bool) $preference->enabled;
    }
}
