<?php

namespace App\Notifications\Tenant\Auth;

use Illuminate\Auth\Notifications\ResetPassword;

/**
 * Class ResetPasswordNotification
 * * Overrides the default Laravel password reset notification to ensure
 * the link points to the Next.js frontend application instead of the API.
 */
class ResetPasswordNotification extends ResetPassword
{
    /**
     * Build the frontend password reset URL.
     *
     * @param  mixed  $notifiable
     * @return string
     */
    protected function resetUrl($notifiable): string
    {
        $frontendUrl = rtrim(config('app.frontend_url'), '/');
        $email = urlencode($notifiable->getEmailForPasswordReset());

        // This matches the query parameters your Next.js page expects: ?token=...&email=...
        return "{$frontendUrl}/reset-password?token={$this->token}&email={$email}";
    }
}
