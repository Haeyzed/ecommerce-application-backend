<?php

namespace App\Http\Controllers\Tenant\Api;

use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\Tenant\CustomerAuth\LoginRequest;
use App\Http\Requests\Tenant\CustomerAuth\RegisterRequest;
use App\Http\Requests\Tenant\CustomerAuth\ForgotPasswordRequest;
use App\Http\Requests\Tenant\CustomerAuth\ResetPasswordRequest;
use App\Services\Tenant\CustomerAuthService;
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
     *
     * @param CustomerAuthService $customerAuthService
     */
    public function __construct(
        private readonly CustomerAuthService $customerAuthService
    ) {}

    /**
     * Register a new customer.
     *
     * @param RegisterRequest $request
     * @return JsonResponse
     */
    public function register(RegisterRequest $request): JsonResponse
    {
        $result = $this->customerAuthService->register($request->validated());

        return ApiResponse::success(
            $result,
            'Customer registered successfully',
            null,
            201
        );
    }

    /**
     * Authenticate an existing customer.
     *
     * @param LoginRequest $request
     * @return JsonResponse
     */
    public function login(LoginRequest $request): JsonResponse
    {
        $result = $this->customerAuthService->login($request->validated());

        return ApiResponse::success(
            $result,
            'Login successful'
        );
    }

    /**
     * Get the currently authenticated customer profile.
     *
     * @param Request $request
     * @return JsonResponse
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
     * Logout the customer by destroying their session.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function logout(Request $request): JsonResponse
    {
        $this->customerAuthService->logout();

        return ApiResponse::success(
            null,
            'Logged out successfully'
        );
    }

    /**
     * Request password reset link.
     *
     * @param ForgotPasswordRequest $request
     * @return JsonResponse
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
     *
     * @param ResetPasswordRequest $request
     * @return JsonResponse
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
     *
     * @param Request $request
     * @param string $id
     * @param string $hash
     * @return JsonResponse
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
     *
     * @param Request $request
     * @return JsonResponse
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
     *
     * @param string $provider
     * @return JsonResponse
     */
    public function redirectToProvider(string $provider): JsonResponse
    {
        $validated = validator(['provider' => $provider], [
            'provider' => 'in:google,facebook,github'
        ])->validate();

        $url = Socialite::driver($provider)->stateless()->redirect()->getTargetUrl();

        return ApiResponse::success(
            ['url' => $url],
            "Redirect URL for {$provider} generated"
        );
    }

    /**
     * Obtain the user information from the provider and log them in as a Customer.
     *
     * @param string $provider
     * @return RedirectResponse
     */
    public function handleProviderCallback(string $provider): RedirectResponse
    {
        $validated = validator(['provider' => $provider], [
            'provider' => 'in:google,facebook,github'
        ])->validate();

        try {
            $socialUser = Socialite::driver($provider)->stateless()->user();

            $this->customerAuthService->handleSocialLogin($provider, $socialUser);

            // Redirect back to the storefront frontend upon successful login
            return redirect()->away(config('app.storefront_url') . '/dashboard');

        } catch (\Exception $e) {
            return redirect()->away(config('app.storefront_url') . '/login?error=social_auth_failed');
        }
    }
}
