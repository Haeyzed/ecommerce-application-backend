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
use Illuminate\Validation\ValidationException;
use Laravel\Socialite\Contracts\User as SocialiteUser;

/**
 * Class CustomerAuthService
 * * Handles storefront authentication, profile management, password resets, and social logins.
 */
class CustomerAuthService
{
    /**
     * Register a new storefront customer.
     *
     * @param array $data Validated registration data.
     * @return array{user: User, profile: Customer, token: string}
     */
    public function register(array $data): array
    {
        $user = User::query()->create([
            'name'      => $data['name'],
            'email'     => $data['email'],
            'password'  => Hash::make($data['password']),
            'is_active' => true,
        ]);

        $customer = Customer::query()->create([
            'user_id' => $user->id,
        ]);

        event(new Registered($user));

        $token = $user->createToken('customer')->plainTextToken;

        return [
            'user'    => $user,
            'profile' => $customer,
            'token'   => $token,
        ];
    }

    /**
     * Authenticate an existing customer.
     *
     * @param array $credentials
     * @return array{user: User, profile: Customer, token: string}
     * @throws ValidationException
     */
    public function login(array $credentials): array
    {
        $user = User::query()->where('email', $credentials['email'])->first();

        if (! $user || ! Hash::check($credentials['password'], $user->password)) {
            throw ValidationException::withMessages([
                'email' => [__('auth.failed')],
            ]);
        }

        if (! $user->is_active || ! optional($user->customer)->is_active) {
            throw ValidationException::withMessages([
                'email' => ['Your account has been deactivated.'],
            ]);
        }

        $token = $user->createToken('customer')->plainTextToken;

        return [
            'user'    => $user,
            'profile' => $user->customer,
            'token'   => $token,
        ];
    }

    /**
     * Send a password reset link to the customer.
     *
     * @param array $data
     * @return string
     * @throws ValidationException
     */
    public function sendPasswordResetLink(array $data): string
    {
        $status = Password::broker()->sendResetLink($data);

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
     * @param array $data
     * @return string
     * @throws ValidationException
     */
    public function resetPassword(array $data): string
    {
        $status = Password::broker()->reset($data, function (User $user, string $password) {
            $user->password = Hash::make($password);
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
     * @param string $id
     * @param string $hash
     * @return string
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
     * @param User $user
     * @return void
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
     * @param string $provider
     * @param SocialiteUser $socialUser
     * @return array{user: User, profile: Customer, token: string}
     */
    public function handleSocialLogin(string $provider, SocialiteUser $socialUser): array
    {
        $user = User::query()->firstOrCreate(
            ['email' => $socialUser->getEmail()],
            [
                'name'              => $socialUser->getName() ?? $socialUser->getNickname() ?? 'Customer',
                'provider'          => $provider,
                'provider_id'       => $socialUser->getId(),
                'password'          => null,
                'email_verified_at' => now(),
                'is_active'         => true,
            ]
        );

        if ($user->provider !== $provider) {
            $user->update([
                'provider'    => $provider,
                'provider_id' => $socialUser->getId(),
            ]);
        }

        $customer = Customer::query()->firstOrCreate([
            'user_id' => $user->id,
        ]);

        $token = $user->createToken('customer')->plainTextToken;

        return [
            'user'    => $user,
            'profile' => $customer,
            'token'   => $token,
        ];
    }
}
