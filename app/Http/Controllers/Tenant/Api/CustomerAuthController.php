<?php

namespace App\Http\Controllers\Tenant\Api;

use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\Tenant\Customer\Auth\ForgotPasswordRequest;
use App\Http\Requests\Tenant\Customer\Auth\LoginRequest;
use App\Http\Requests\Tenant\Customer\Auth\RegisterRequest;
use App\Http\Requests\Tenant\Customer\Auth\ResetPasswordRequest;
use App\Services\Tenant\CustomerAuthService;
use Exception;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Laravel\Socialite\Facades\Socialite;

/**
 * Customer Auth Endpoints
 * * Handles registration, authentication, profile retrieval, password resets, and social logins for storefront customers.
 */
class CustomerAuthController extends Controller
{
    /**
     * Create a new CustomerController instance.
     */
    public function __construct(
        private readonly CustomerAuthService $customerAuthService
    ) {}

    /**
     * Register a new customer.
     */
    public function register(RegisterRequest $request): JsonResponse
    {
        $result = $this->customerAuthService->register($request->validated());

        return ApiResponse::success(
            [
                'user' => $result['user'],
                'profile' => $result['profile'],
                'token' => $result['token'],
            ],
            'Customer registered successfully',
            null,
            201
        );
    }

    /**
     * Authenticate an existing customer.
     */
    public function login(LoginRequest $request): JsonResponse
    {
        $result = $this->customerAuthService->login($request->validated());

        return ApiResponse::success(
            [
                'user' => $result['user'],
                'profile' => $result['profile'],
                'token' => $result['token'],
            ],
            'Login successful'
        );
    }

    /**
     * Get the currently authenticated customer profile.
     */
    public function me(Request $request): JsonResponse
    {
        $user = $request->user()->load('customer');

        return ApiResponse::success(
            ['user' => $user],
            'Customer profile retrieved successfully'
        );
    }

    /**
     * Logout the customer by revoking the current Sanctum token.
     */
    public function logout(Request $request): JsonResponse
    {
        $this->customerAuthService->logout($request->user());

        return ApiResponse::success(
            null,
            'Logged out successfully'
        );
    }

    /**
     * Request password reset link.
     */
    public function forgotPassword(ForgotPasswordRequest $request): JsonResponse
    {
        $status = $this->customerAuthService->sendPasswordResetLink($request->validated());

        return ApiResponse::success(
            ['status' => $status],
            'Password reset link sent successfully'
        );
    }

    /**
     * Reset password.
     */
    public function resetPassword(ResetPasswordRequest $request): JsonResponse
    {
        $status = $this->customerAuthService->resetPassword($request->validated());

        return ApiResponse::success(
            ['status' => $status],
            'Password reset successful'
        );
    }

    /**
     * Verify email address.
     */
    public function verify(Request $request, string $id, string $hash): JsonResponse
    {
        try {
            $status = $this->customerAuthService->verifyEmail($id, $hash);

            return ApiResponse::success(
                ['status' => $status],
                'Email verification processed'
            );

        } catch (AuthorizationException $e) {
            return ApiResponse::error($e->getMessage(), null, 403);
        }
    }

    /**
     * Resend verification email.
     */
    public function resendVerification(Request $request): JsonResponse
    {
        $this->customerAuthService->resendVerificationEmail($request->user());

        return ApiResponse::success(
            ['status' => 'verification-link-sent'],
            'Verification email sent successfully'
        );
    }

    /**
     * Redirect the user to the provider's authentication page.
     */
    public function redirectToProvider(string $provider): JsonResponse
    {
        $validated = validator(['provider' => $provider], [
            'provider' => 'in:google,facebook,github',
        ])->validate();

        $url = Socialite::driver($provider)->stateless()->redirect()->getTargetUrl();

        return ApiResponse::success(
            ['url' => $url],
            "Redirect URL for {$provider} generated"
        );
    }

    /**
     * Obtain the user information from the provider and log them in as a Customer.
     */
    public function handleProviderCallback(string $provider): RedirectResponse
    {
        $validated = validator(['provider' => $provider], [
            'provider' => 'in:google,facebook,github',
        ])->validate();

        try {
            $socialUser = Socialite::driver($provider)->stateless()->user();

            $result = $this->customerAuthService->handleSocialLogin($provider, $socialUser);

            $base = rtrim((string) config('app.storefront_url'), '/');
            $fragment = 'access_token='.rawurlencode($result['token']);

            return redirect()->away($base.'/auth/callback#'.$fragment);

        } catch (Exception $e) {
            return redirect()->away(config('app.storefront_url').'/login?error=social_auth_failed');
        }
    }
}
