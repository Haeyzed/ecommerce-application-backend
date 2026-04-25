<?php

namespace App\Services\Tenant;

use App\Models\Tenant\Customer;
use App\Models\Tenant\User;
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
 * Class CustomerService
 * * Handles storefront authentication, profile management, password resets, and social logins via the base User model.
 */
class CustomerAuthService
{
    /**
     * Register a new storefront customer.
     * * Creates the underlying User, attaches a Customer profile, and issues a Sanctum token.
     *
     * @param  array  $data  Validated registration data.
     * @return array{user: User, profile: Customer, token: string}
     */
    public function register(array $data): array
    {
        $user = User::query()->create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
        ]);

        $customer = Customer::query()->create([
            'user_id' => $user->id,
        ]);

        event(new Registered($user));

        $token = $user->createToken('customer')->plainTextToken;

        return [
            'user' => $user,
            'profile' => $customer,
            'token' => $token,
        ];
    }

    /**
     * Authenticate an existing storefront customer via Sanctum token.
     * * Ensures they have a Customer profile.
     *
     * @param  array  $credentials  Validated login credentials.
     * @return array{user: User, profile: Customer, token: string}
     *
     * @throws ValidationException
     */
    public function login(array $credentials): array
    {
        $user = User::query()->where('email', $credentials['email'])->whereHas('customer')->first();

        if (! $user || ! Hash::check($credentials['password'], $user->password)) {
            throw ValidationException::withMessages([
                'email' => [__('auth.failed')],
            ]);
        }

        $customer = Customer::query()->firstOrCreate([
            'user_id' => $user->id,
        ]);

        $token = $user->createToken('customer')->plainTextToken;

        return [
            'user' => $user,
            'customer' => $customer,
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
     * Send a password reset link to the given customer.
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
     * Reset the customer's password.
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
     * Verify the customer's email address using the signed URL parameters.
     *
     * @param  string  $id  The user ID.
     * @param  string  $hash  The verification hash.
     *
     * @throws AuthorizationException
     */
    public function verifyEmail(string $id, string $hash): string
    {
        $user = User::query()->findOrFail($id);

        if (! hash_equals($hash, sha1($user->getEmailForVerification()))) {
            throw new AuthorizationException('Invalid verification link.');
        }

        if ($user->hasVerifiedEmail()) {
            return 'already-verified';
        }

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
     * Handle socialite user login or registration for a storefront customer.
     *
     * @return array{user: User, profile: Customer, token: string}
     */
    public function handleSocialLogin(string $provider, SocialiteUser $socialUser): array
    {
        $user = User::query()->firstOrCreate(
            ['email' => $socialUser->getEmail()],
            [
                'name' => $socialUser->getName() ?? $socialUser->getNickname() ?? 'Customer',
                'provider' => $provider,
                'provider_id' => $socialUser->getId(),
                'password' => null,
                'email_verified_at' => now(),
            ]
        );

        if ($user->provider !== $provider) {
            $user->update([
                'provider' => $provider,
                'provider_id' => $socialUser->getId(),
            ]);
        }

        $customer = Customer::query()->firstOrCreate([
            'user_id' => $user->id,
        ]);

        $token = $user->createToken('customer')->plainTextToken;

        return [
            'user' => $user,
            'profile' => $customer,
            'token' => $token,
        ];
    }
}
