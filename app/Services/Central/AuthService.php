<?php

namespace App\Services\Central;

use App\Models\Central\User;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Events\Verified;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Laravel\Socialite\Contracts\User as SocialiteUser;

/**
 * Class AuthService
 * * Handles all core business logic related to user authentication.
 */
class AuthService
{
    /**
     * Handle user registration.
     *
     * @param  array  $data  Validated registration data.
     * @return array{user: User, token: string}
     */
    public function register(array $data): array
    {
        $user = User::query()->create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
        ]);

        event(new Registered($user));

        $token = $user->createToken('auth')->plainTextToken;

        return [
            'user' => $user,
            'token' => $token,
        ];
    }

    /**
     * Handle user login via Sanctum personal access token.
     *
     * @param  array  $credentials  Validated login credentials.
     * @return array{user: User, token: string}
     *
     * @throws ValidationException
     */
    public function login(array $credentials): array
    {
        $user = User::query()->where('email', $credentials['email'])->first();

        if (! $user || ! Hash::check($credentials['password'], $user->password)) {
            throw ValidationException::withMessages([
                'email' => __('auth.failed'),
            ]);
        }

        $token = $user->createToken('auth')->plainTextToken;

        return [
            'user' => $user,
            'token' => $token,
        ];
    }

    /**
     * Revoke the current Sanctum access token.
     */
    public function logout(User $user): void
    {
        $user->currentAccessToken()?->delete();
    }

    /**
     * Send a password reset link to the given user.
     *
     * @param  array  $data  Array containing the user's email.
     * @return string The status translation string.
     *
     * @throws ValidationException
     */
    public function sendPasswordResetLink(array $data): string
    {
        $status = Password::broker('users')->sendResetLink($data);

        if ($status !== Password::RESET_LINK_SENT) {
            throw ValidationException::withMessages([
                'email' => [__($status)],
            ]);
        }

        return __($status);
    }

    /**
     * Reset the user's password.
     *
     * @param  array  $data  Validated reset token, email, and new password.
     * @return string The status translation string.
     *
     * @throws ValidationException
     */
    public function resetPassword(array $data): string
    {
        $status = Password::broker('users')->reset($data, function (User $user, string $password) {
            $user->password = Hash::make($password);
            $user->setRememberToken(Str::random(60));
            $user->save();

            event(new PasswordReset($user));
        });

        if ($status !== Password::PASSWORD_RESET) {
            throw ValidationException::withMessages([
                'email' => [__($status)],
            ]);
        }

        return __($status);
    }

    /**
     * Verify the user's email address using the signed URL parameters.
     *
     * @param  string  $id  The user ID.
     * @param  string  $hash  The verification hash.
     *
     * @throws AuthorizationException
     */
    public function verifyEmail(string $id, string $hash): string
    {
        $user = User::query()->findOrFail($id);

        // Validate the hash against the user's email
        if (! hash_equals($hash, sha1($user->getEmailForVerification()))) {
            throw new AuthorizationException('Invalid verification link.');
        }

        // If already verified, quietly return
        if ($user->hasVerifiedEmail()) {
            return 'already-verified';
        }

        // Mark as verified and dispatch the event
        $user->markEmailAsVerified();
        event(new Verified($user));

        return 'verified';
    }

    /**
     * Resend the email verification notification.
     *
     * @param  User  $user  The authenticated user model.
     */
    public function resendVerificationEmail(User $user): void
    {
        if ($user->hasVerifiedEmail()) {
            return;
        }

        $user->sendEmailVerificationNotification();
    }

    /**
     * Handle socialite user login or registration.
     *
     * @return array{user: User, token: string}
     */
    public function handleSocialLogin(string $provider, SocialiteUser $socialUser): array
    {
        $user = User::query()->firstOrCreate(
            ['email' => $socialUser->getEmail()],
            [
                'name' => $socialUser->getName() ?? $socialUser->getNickname() ?? 'User',
                'provider' => $provider,
                'provider_id' => $socialUser->getId(),
                'password' => null,
                'email_verified_at' => now(),
            ]
        );

        // Update provider details if they logged in with a different method previously
        if ($user->provider !== $provider) {
            $user->update([
                'provider' => $provider,
                'provider_id' => $socialUser->getId(),
            ]);
        }

        $token = $user->createToken('auth')->plainTextToken;

        return [
            'user' => $user,
            'token' => $token,
        ];
    }
}
