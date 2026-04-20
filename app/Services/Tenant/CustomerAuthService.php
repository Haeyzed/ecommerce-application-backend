<?php

namespace App\Services\Tenant;

use App\Models\Tenant\Customer;
use App\Models\Tenant\User;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Events\Verified;
use Illuminate\Support\Facades\Auth;
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
     * * Creates the underlying User, attaches a Customer profile, and logs them in via session.
     *
     * @param array $data Validated registration data.
     * @return array Contains 'user' and 'profile'.
     */
    public function register(array $data): array
    {
        $user = User::query()->create([
            'name'     => $data['name'],
            'email'    => $data['email'],
            'password' => Hash::make($data['password']),
        ]);

        $customer = Customer::query()->create([
            'user_id' => $user->id,
        ]);

        event(new Registered($user));

        Auth::login($user);

        return [
            'user'    => $user,
            'profile' => $customer,
        ];
    }

    /**
     * Authenticate an existing storefront customer via session.
     * * Attempts login, regenerates the session, and ensures they have a Customer profile.
     *
     * @param array $credentials Validated login credentials.
     * @return array Contains 'user' and 'profile'.
     * @throws ValidationException
     */
    public function login(array $credentials): array
    {
        if (! Auth::attempt($credentials)) {
            throw ValidationException::withMessages([
                'email' => [__('auth.failed')],
            ]);
        }

        request()->session()->regenerate();

        $user = Auth::user();

        $customer = Customer::query()->firstOrCreate([
            'user_id' => $user->id,
        ]);

        return [
            'user'    => $user,
            'profile' => $customer,
        ];
    }

    /**
     * Logout a customer by invalidating their session.
     *
     * @return void
     */
    public function logout(): void
    {
        Auth::guard('web')->logout();

        request()->session()->invalidate();
        request()->session()->regenerateToken();
    }

    /**
     * Send a password reset link to the given customer.
     *
     * @param array $data Array containing the user's email.
     * @return string The status translation string.
     * @throws ValidationException
     */
    public function sendPasswordResetLink(array $data): string
    {
        $status = Password::sendResetLink($data);

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
     * @param array $data Validated reset token, email, and new password.
     * @return string The status translation string.
     * @throws ValidationException
     */
    public function resetPassword(array $data): string
    {
        $status = Password::reset($data, function (User $user, string $password) {
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
     * @param string $id The user ID.
     * @param string $hash The verification hash.
     * @return string
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
     * @param User $user The authenticated user model.
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
     * * Creates the User if missing, attaches the Customer profile, and logs them in.
     *
     * @param string $provider
     * @param SocialiteUser $socialUser
     * @return array Contains 'user' and 'profile'.
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

        Auth::login($user);
        request()->session()->regenerate();

        return [
            'user'    => $user,
            'profile' => $customer,
        ];
    }
}
