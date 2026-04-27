<?php

namespace App\Notifications\Tenant\Auth;

use App\Models\Tenant\NotificationTemplate;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\HtmlString;

/**
 * Class ResetPasswordNotification
 * * Overrides the default Laravel password reset notification to use both
 * the database template and the Next.js frontend URL.
 */
class ResetPasswordNotification extends ResetPassword
{
    /**
     * Build the frontend password reset URL.
     */
    protected function resetUrl($notifiable): string
    {
        $frontendUrl = rtrim(config('app.frontend_url'), '/');
        $email = urlencode($notifiable->getEmailForPasswordReset());

        return "{$frontendUrl}/reset-password?token={$this->token}&email={$email}";
    }

    /**
     * Build the mail representation of the notification using the DB template.
     *
     * @param  mixed  $notifiable
     * @return MailMessage
     */
    public function toMail($notifiable): MailMessage
    {
        // 1. Generate the Next.js reset link
        $resetLink = $this->resetUrl($notifiable);

        // 2. Fetch the DB template
        $template = NotificationTemplate::query()
            ->where('event', 'password_reset')
            ->where('channel', 'email')
            ->where('is_active', true)
            ->first();

        if (!$template) {
            // Fallback to Laravel's default if template is deleted
            return parent::toMail($notifiable);
        }

        // 3. Prepare the data payload
        $data = [
            'name'  => $notifiable->name ?? 'User',
            'token' => $this->token, // For raw token entry
            'link'  => $resetLink,   // In case you want to use a {link} variable later
        ];

        // 4. Parse variables
        $subject = $this->parseVariables($template->subject ?? 'Reset Password', $data);
        $body = $this->parseVariables($template->body, $data);

        // 5. Convert to HTML and add the Reset Button
        $formattedBody = nl2br(e($body));

        return (new MailMessage)
            ->subject($subject)
            ->line(new HtmlString($formattedBody))
            ->action('Reset Password', $resetLink); // Appends the standard Laravel action button
    }

    private function parseVariables(string $text, array $data): string
    {
        $replacements = [];
        foreach ($data as $key => $value) {
            $replacements['{' . $key . '}'] = $value;
        }

        return strtr($text, $replacements);
    }
}
