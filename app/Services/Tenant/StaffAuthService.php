<?php

namespace App\Services\Tenant;

use App\Models\Tenant\Staff;
use App\Models\Tenant\User;
use App\Notifications\Tenant\DynamicTemplateNotification;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Events\Verified;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

/**
 * Class StaffAuthService
 * * Handles staff authentication, profile management, and password resets via the base User model.
 */
class StaffAuthService
{
    /**
     * Register a new staff member.
     * * Creates the underlying User, attaches a Staff profile, and issues a Sanctum token.
     *
     * @param  array  $data  Validated registration data.
     * @return array{user: User, profile: Staff, token: string}
     */
    public function register(array $data): array
    {
        $rawPassword = $data['password'] ?? Str::random(8);

        $user = User::query()->create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => $data['password'],
            'is_active' => true,
        ]);

        $staff = Staff::query()->create([
            'user_id' => $user->id,
            'phone' => $data['phone'] ?? null,
            'currency' => $data['currency'] ?? 'USD',
            'locale' => $data['locale'] ?? 'en',
            'is_active' => true,
        ]);

        event(new Registered($user));

        $user->notify(new DynamicTemplateNotification(
            event: 'staff_registered',
            templateData: [
                'name' => $user->name,
                'store_name' => config('app.name', 'Our Store'), // Or get tenant name
                'email' => $user->email,
                'password' => $rawPassword,
            ]
        ));

        $token = $user->createToken('staff')->plainTextToken;

        return [
            'user' => $user,
            'profile' => $staff,
            'token' => $token,
        ];
    }

    /**
     * Authenticate an existing staff member via Sanctum token.
     * * Ensures they have a Staff profile.
     *
     * @param  array  $credentials  Validated login credentials.
     * @return array{user: User, profile: Staff, token: string}
     *
     * @throws ValidationException
     */
    public function login(array $credentials): array
    {
        $user = User::query()
            ->where('email', $credentials['email'])
            ->whereHas('staff')
            ->first();

        if (! $user || ! Hash::check($credentials['password'], $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }

        if (! $user->is_active || ! optional($user->staff)->is_active) {
            throw ValidationException::withMessages([
                'email' => ['This staff account is inactive.'],
            ]);
        }

        $token = $user->createToken('staff')->plainTextToken;

        return [
            'user' => $user->load('staff'),
            'profile' => $user->staff,
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
     * Send a password reset link to the given staff member.
     *
     * @param  array  $data  Array containing the user's email.
     * @return string The status translation string.
     *
     * @throws ValidationException
     */
    public function sendPasswordResetLink(array $data): string
    {
        $status = Password::broker('tenant_users')->sendResetLink($data);

        if ($status !== Password::RESET_LINK_SENT) {
            throw ValidationException::withMessages([
                'email' => [__($status)],
            ]);
        }

        return __($status);
    }

    /**
     * Reset the staff member's password.
     *
     * @param  array  $data  Validated reset token, email, and new password.
     * @return string The status translation string.
     *
     * @throws ValidationException
     */
    public function resetPassword(array $data): string
    {
        $status = Password::broker('tenant_users')->reset($data, function (User $user, string $password) {
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
     * Verify the staff member's email address using the signed URL parameters.
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
}
