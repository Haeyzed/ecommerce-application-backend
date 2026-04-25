<?php

namespace App\Http\Controllers\Central\Api;

use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\Central\Auth\ForgotPasswordRequest;
use App\Http\Requests\Central\Auth\LoginRequest;
use App\Http\Requests\Central\Auth\RegisterRequest;
use App\Http\Requests\Central\Auth\ResetPasswordRequest;
use App\Http\Resources\Central\UserResource;
use App\Services\Central\AuthService;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Laravel\Socialite\Facades\Socialite;

/**
 * Authentication Endpoints
 * Handles user registration, authentication, and password management
 * using Laravel Sanctum personal access tokens (Bearer API).
 */
class AuthController extends Controller
{
    /**
     * Create a new AuthController instance.
     */
    public function __construct(
        private readonly AuthService $authService
    ) {}

    /**
     * Register a new user.
     * Creates a new user account, triggers the verification email,
     * and returns a Sanctum access token.
     *
     * * @unauthenticated
     */
    public function register(RegisterRequest $request): JsonResponse
    {
        $result = $this->authService->register($request->validated());

        return ApiResponse::success(
            [
                'user' => new UserResource($result['user']),
                'token' => $result['token'],
            ],
            'User registered successfully',
            null,
            201
        );
    }

    /**
     * Login user.
     * Authenticates the user and returns a Sanctum access token.
     *
     * * @unauthenticated
     */
    public function login(LoginRequest $request): JsonResponse
    {
        $result = $this->authService->login($request->validated());

        return ApiResponse::success(
            [
                'user' => new UserResource($result['user']),
                'token' => $result['token'],
            ],
            'Login successful'
        );
    }

    /**
     * Logout user.
     * Revokes the current Sanctum access token.
     */
    public function logout(Request $request): JsonResponse
    {
        $this->authService->logout($request->user());

        return ApiResponse::success(null, 'Logout successful');
    }

    /**
     * Get current user.
     * Retrieves the profile information for the currently authenticated user.
     */
    public function me(Request $request): JsonResponse
    {
        return ApiResponse::success(
            new UserResource($request->user()),
            'Authenticated user fetched successfully'
        );
    }

    /**
     * Request password reset link.
     * Sends an email containing a secure link to reset the user's password.
     *
     * * @unauthenticated
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
     * Updates the user's password using the token provided via email.
     *
     * * @unauthenticated
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
     * Validates the signed URL and marks the user's email as verified.
     *
     * * @unauthenticated
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
     * Triggers a new email verification link to be sent to the authenticated user.
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
     * Returns the URL for the frontend to handle the redirect to avoid CORS issues.
     */
    public function redirectToProvider(string $provider): JsonResponse
    {
        $validated = validator(['provider' => $provider], [
            'provider' => 'in:google,facebook,github',
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
     * Handles the callback, issues a Sanctum token, and redirects to the SPA with `#access_token=...`.
     */
    public function handleProviderCallback(string $provider): RedirectResponse
    {
        $validated = validator(['provider' => $provider], [
            'provider' => 'in:google,facebook,github',
        ])->validate();

        try {
            $socialUser = Socialite::driver($provider)->stateless()->user();

            $result = $this->authService->handleSocialLogin($provider, $socialUser);

            $base = rtrim((string) config('app.frontend_url'), '/');
            $fragment = 'access_token='.rawurlencode($result['token']);

            return redirect()->away($base.'/auth/callback#'.$fragment);

        } catch (\Exception $e) {
            // Redirect back to frontend login page with an error parameter
            return redirect()->away(config('app.frontend_url').'/login?error=social_auth_failed');
        }
    }
}
