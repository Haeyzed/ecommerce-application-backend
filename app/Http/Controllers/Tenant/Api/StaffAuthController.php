<?php

namespace App\Http\Controllers\Tenant\Api;

use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\Tenant\Staff\Auth\ForgotPasswordRequest;
use App\Http\Requests\Tenant\Staff\Auth\LoginRequest;
use App\Http\Requests\Tenant\Staff\Auth\RegisterRequest;
use App\Http\Requests\Tenant\Staff\Auth\ResetPasswordRequest;
use App\Services\Tenant\StaffAuthService;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Admin Auth Endpoints
 * * Handles registration, authentication, profile retrieval, and password resets for internal store admin.
 */
class StaffAuthController extends Controller
{
    /**
     * Create a new StaffAuthController instance.
     */
    public function __construct(
        private readonly StaffAuthService $staffAuthService
    ) {}

    /**
     * Register a new admin member.
     */
    public function register(RegisterRequest $request): JsonResponse
    {
        $result = $this->staffAuthService->register($request->validated());

        return ApiResponse::success(
            [
                'user' => $result['user'],
                'profile' => $result['profile'],
                'token' => $result['token'],
            ],
            'Admin registered successfully',
            null,
            201
        );
    }

    /**
     * Authenticate an existing admin member.
     */
    public function login(LoginRequest $request): JsonResponse
    {
        $result = $this->staffAuthService->login($request->validated());

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
     * Get the currently authenticated admin profile.
     */
    public function me(Request $request): JsonResponse
    {
        $user = $request->user()->load('staff');

        return ApiResponse::success(
            ['user' => $user],
            'Admin profile retrieved successfully'
        );
    }

    /**
     * Logout the admin member by revoking the current Sanctum token.
     */
    public function logout(Request $request): JsonResponse
    {
        $this->staffAuthService->logout($request->user());

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
        $status = $this->staffAuthService->sendPasswordResetLink($request->validated());

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
        $status = $this->staffAuthService->resetPassword($request->validated());

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
            $status = $this->staffAuthService->verifyEmail($id, $hash);

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
        $this->staffAuthService->resendVerificationEmail($request->user());

        return ApiResponse::success(
            ['status' => 'verification-link-sent'],
            'Verification email sent successfully'
        );
    }
}
