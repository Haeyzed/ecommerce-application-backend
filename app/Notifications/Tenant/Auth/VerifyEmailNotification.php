<?php

namespace App\Notifications\Tenant\Auth;

use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\URL;

/**
 * Class VerifyEmailNotification
 * * Overrides the default Laravel email verification notification. It generates
 * the signed API route and passes it to the Next.js frontend as a query parameter.
 */
class VerifyEmailNotification extends VerifyEmail
{
    /**
     * Build the verification URL.
     *
     * @param  mixed  $notifiable
     * @return string
     */
    protected function verificationUrl($notifiable): string
    {
        if (static::$createUrlCallback) {
            return call_user_func(static::$createUrlCallback, $notifiable);
        }

        // 1. Generate the standard secure signed route for the Laravel API
        $apiUrl = URL::temporarySignedRoute(
            'verification.verify',
            Carbon::now()->addMinutes(Config::get('auth.verification.expire', 60)),
            [
                'id' => $notifiable->getKey(),
                'hash' => sha1($notifiable->getEmailForVerification()),
            ]
        );

        // 2. Wrap it in your Next.js frontend URL
        $frontendUrl = rtrim(config('app.frontend_url'), '/');

        return "{$frontendUrl}/verify-email?verify_url=" . urlencode($apiUrl);
    }
}
