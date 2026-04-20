<?php

namespace App\Http\Controllers\Tenant\Api;

use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\Tenant\Auth\ForgotPasswordRequest;
use App\Http\Requests\Tenant\Auth\LoginRequest;
use App\Http\Requests\Tenant\Auth\RegisterRequest;
use App\Http\Requests\Tenant\Auth\ResetPasswordRequest;
use App\Http\Resources\Tenant\UserResource;
use App\Services\Tenant\AuthService;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Laravel\Socialite\Facades\Socialite;

/**
 * Authentication Endpoints
 * * Handles user registration, authentication, and password management
 * for the Next.js SPA frontend using Laravel Sanctum.
 */
class AuthController extends Controller
{
    /**
     * Create a new AuthController instance.
     *
     * @param AuthService $authService
     */
    public function __construct(
        private readonly AuthService $authService
    ) {}

    /**
     * Register a new user.
     * * Creates a new user account, triggers the verification email,
     * and initializes a logged-in session.
     * * @unauthenticated
     *
     * @param RegisterRequest $request
     * @return JsonResponse
     */
    public function register(RegisterRequest $request): JsonResponse
    {
        $user = $this->authService->register($request->validated());

        return ApiResponse::success(
            ['user' => new UserResource($user)],
            'User registered successfully',
            null,
            201
        );
    }

    /**
     * Login user.
     * * Authenticates the user and initializes a secure session via cookies.
     * * @unauthenticated
     *
     * @param LoginRequest $request
     * @return JsonResponse
     */
    public function login(LoginRequest $request): JsonResponse
    {
        $user = $this->authService->login($request->validated());

        return ApiResponse::success(
            ['user' => new UserResource($user)],
            'Login successful'
        );
    }

    /**
     * Logout user.
     * * Invalidates the current session and clears the authentication cookies.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function logout(Request $request): JsonResponse
    {
        $this->authService->logout();

        return ApiResponse::success(null, 'Logout successful');
    }

    /**
     * Get current user.
     * * Retrieves the profile information for the currently authenticated user.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function me(Request $request): JsonResponse
    {
        return ApiResponse::success(
            ['user' => new UserResource($request->user())],
            'Authenticated user fetched successfully'
        );
    }

    /**
     * Request password reset link.
     * * Sends an email containing a secure link to reset the user's password.
     * * @unauthenticated
     *
     * @param ForgotPasswordRequest $request
     * @return JsonResponse
     */
    public function forgotPassword(ForgotPasswordRequest $request): JsonResponse
    {
        $status = $this->authService->sendPasswordResetLink($request->validated());

        return ApiResponse::success(
            ['status' => $status],
            'Password reset link sent successfully'
        );
    }

    /**
     * Reset password.
     * * Updates the user's password using the token provided via email.
     * * @unauthenticated
     *
     * @param ResetPasswordRequest $request
     * @return JsonResponse
     */
    public function resetPassword(ResetPasswordRequest $request): JsonResponse
    {
        $status = $this->authService->resetPassword($request->validated());

        return ApiResponse::success(
            ['status' => $status],
            'Password reset successful'
        );
    }

    /**
     * Verify email address.
     * * Validates the signed URL and marks the user's email as verified.
     * * @unauthenticated
     *
     * @param Request $request
     * @param string $id
     * @param string $hash
     * @return JsonResponse
     */
    public function verify(Request $request, string $id, string $hash): JsonResponse
    {
        try {
            $status = $this->authService->verifyEmail($id, $hash);

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
     * * Triggers a new email verification link to be sent to the authenticated user.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function resendVerification(Request $request): JsonResponse
    {
        $this->authService->resendVerificationEmail($request->user());

        return ApiResponse::success(
            ['status' => 'verification-link-sent'],
            'Verification email sent successfully'
        );
    }

    /**
     * Redirect the user to the provider's authentication page.
     * * Returns the URL for the frontend to handle the redirect to avoid CORS issues.
     *
     * @param string $provider
     * @return JsonResponse
     */
    public function redirectToProvider(string $provider): JsonResponse
    {
        $validated = validator(['provider' => $provider], [
            'provider' => 'in:google,facebook,github'
        ])->validate();

        // Get the stateless redirect URL from Socialite
        $url = Socialite::driver($provider)->stateless()->redirect()->getTargetUrl();

        return ApiResponse::success(
            ['url' => $url],
            "Redirect URL for {$provider} generated"
        );
    }

    /**
     * Obtain the user information from the provider.
     * * Handles the callback, logs the user in, and redirects to the Next.js frontend.
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

            // Handle the login/registration via the service
            $this->authService->handleSocialLogin($provider, $socialUser);

            // Redirect back to the Next.js frontend upon successful login
            return redirect()->away(config('app.frontend_url') . '/dashboard');

        } catch (\Exception $e) {
            // Redirect back to frontend login page with an error parameter
            return redirect()->away(config('app.frontend_url') . '/login?error=social_auth_failed');
        }
    }
}
