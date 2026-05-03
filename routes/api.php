<?php

use App\Http\Controllers\Central\Api\AuditLogController;
use App\Http\Controllers\Central\Api\AuthController;
use App\Http\Controllers\Central\Api\DomainController;
use App\Http\Controllers\Central\Api\InvoiceController;
use App\Http\Controllers\Central\Api\NotificationController;
use App\Http\Controllers\Central\Api\OnboardingController;
use App\Http\Controllers\Central\Api\PlanController;
use App\Http\Controllers\Central\Api\SettingController; // Import the Central SettingController
use App\Http\Controllers\Central\Api\SubscriptionController;
use App\Http\Controllers\Central\Api\TenantController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Central SaaS API (separate database)
|--------------------------------------------------------------------------
*/

Route::prefix('central')->name('central.')->group(function (): void {
    Route::post('/register', [AuthController::class, 'register'])->name('register');
    Route::post('/login', [AuthController::class, 'login'])->name('login');
    Route::post('/forgot-password', [AuthController::class, 'forgotPassword'])->name('password.email');
    Route::post('/reset-password', [AuthController::class, 'resetPassword'])->name('password.update');

    Route::get('/auth/{provider}/redirect', [AuthController::class, 'redirectToProvider'])->name('oauth.redirect');
    Route::get('/auth/{provider}/callback', [AuthController::class, 'handleProviderCallback'])->name('oauth.callback');

    Route::post('/onboarding', [OnboardingController::class, 'store'])->name('onboarding.store');

    Route::middleware('auth:sanctum')->group(function (): void {
        Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
        Route::get('/me', [AuthController::class, 'me'])->name('me');

        Route::post('/email/verification-notification', [AuthController::class, 'resendVerification'])
            ->middleware('throttle:6,1')
            ->name('verification.send');

        Route::get('plans/dropdown', [PlanController::class, 'dropdown'])->name('plans.dropdown');
        Route::get('tenants/dropdown', [TenantController::class, 'dropdown'])->name('tenants.dropdown');
        Route::get('subscriptions/statuses/dropdown', [SubscriptionController::class, 'statusDropdown'])->name('subscriptions.statuses.dropdown');
        Route::get('subscriptions/roles/dropdown', [SubscriptionController::class, 'roleDropdown'])->name('subscriptions.roles.dropdown');
        Route::get('notification-templates/dropdown', [NotificationController::class, 'dropdown'])->name('notification-templates.dropdown');

        Route::apiResource('plans', PlanController::class);

        Route::apiResource('tenants', TenantController::class);
        Route::get('tenants/{tenant}/domains', [DomainController::class, 'index'])->name('tenants.domains.index');
        Route::post('tenants/{tenant}/domains', [DomainController::class, 'store'])->name('tenants.domains.store');
        Route::delete('tenants/{tenant}/domains/{domain}', [DomainController::class, 'destroy'])
            ->where('domain', '[a-zA-Z0-9._\-]+')
            ->name('tenants.domains.destroy');

        Route::get('subscriptions', [SubscriptionController::class, 'index'])->name('subscriptions.index');
        Route::post('subscriptions/trial', [SubscriptionController::class, 'startTrial'])->name('subscriptions.trial');
        Route::post('subscriptions/{subscription}/activate', [SubscriptionController::class, 'activate'])->name('subscriptions.activate');
        Route::post('subscriptions/{subscription}/cancel', [SubscriptionController::class, 'cancel'])->name('subscriptions.cancel');
        Route::post('subscriptions/{subscription}/invoice', [SubscriptionController::class, 'invoice'])->name('subscriptions.invoice');

        Route::get('invoices', [InvoiceController::class, 'index'])->name('invoices.index');
        Route::get('invoices/{id}', [InvoiceController::class, 'show'])->whereNumber('id')->name('invoices.show');

        Route::get('audit-logs', [AuditLogController::class, 'index'])->name('audit-logs.index');

        // Central Settings Routes
        Route::get('/settings', [SettingController::class, 'show'])->name('settings.show');
        Route::post('/settings', [SettingController::class, 'update'])->name('settings.update');
    });
});

Route::get('/email/verify/{id}/{hash}', [AuthController::class, 'verify'])
    ->middleware(['signed', 'throttle:6,1'])
    ->name('verification.verify');
