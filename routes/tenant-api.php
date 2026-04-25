<?php

declare(strict_types=1);

use App\Http\Controllers\Tenant\Api\CMS\BlogController;
use App\Http\Controllers\Tenant\Api\CMS\PageController;
use App\Http\Controllers\Tenant\Api\CartController;
use App\Http\Controllers\Tenant\Api\CustomerAuthController;
use App\Http\Controllers\Tenant\Api\HR\ApplicantController;
use App\Http\Controllers\Tenant\Api\HR\AttendanceController;
use App\Http\Controllers\Tenant\Api\HR\DepartmentController;
use App\Http\Controllers\Tenant\Api\HR\EmployeeController;
use App\Http\Controllers\Tenant\Api\HR\EmployeeDocumentController;
use App\Http\Controllers\Tenant\Api\HR\InterviewController;
use App\Http\Controllers\Tenant\Api\HR\JobPostingController;
use App\Http\Controllers\Tenant\Api\HR\LeaveController;
use App\Http\Controllers\Tenant\Api\HR\PayrollController;
use App\Http\Controllers\Tenant\Api\HR\PerformanceController;
use App\Http\Controllers\Tenant\Api\HR\PositionController;
use App\Http\Controllers\Tenant\Api\HR\TrainingController;
use App\Http\Controllers\Tenant\Api\Notification\NotificationPreferenceController;
use App\Http\Controllers\Tenant\Api\Notification\NotificationTemplateController;
use App\Http\Controllers\Tenant\Api\SettingController;
use App\Http\Controllers\Tenant\Api\StaffAuthController;
use Illuminate\Support\Facades\Route;
use Stancl\Tenancy\Middleware\InitializeTenancyByDomain;
use Stancl\Tenancy\Middleware\PreventAccessFromCentralDomains;

/*
|--------------------------------------------------------------------------
| Tenant API (database-per-tenant)
|--------------------------------------------------------------------------
*/

Route::middleware([
    'api',
    InitializeTenancyByDomain::class,
    PreventAccessFromCentralDomains::class,
])->prefix('api')->name('tenant.')->group(function () {

    // --- Settings ---
    Route::get('/settings', [SettingController::class, 'show'])->name('settings.show');
    Route::put('/settings', [SettingController::class, 'update'])
        ->middleware('auth:sanctum')
        ->name('settings.update');

    // --- Cart Module ---
    Route::prefix('cart')->name('cart.')->group(function () {
        Route::get('/', [CartController::class, 'show'])->name('show');
        Route::post('/items', [CartController::class, 'addItem'])->name('items.store');
        Route::patch('/items/{itemId}', [CartController::class, 'updateItem'])->name('items.update');
        Route::delete('/items/{itemId}', [CartController::class, 'removeItem'])->name('items.destroy');
        Route::post('/coupon', [CartController::class, 'applyCoupon'])->name('coupon.apply');
    });

    // --- Customer Auth ---
    Route::prefix('customer')->name('customer.')->group(function () {
        Route::post('/register', [CustomerAuthController::class, 'register'])->name('register');
        Route::post('/login', [CustomerAuthController::class, 'login'])->name('login');
        Route::post('/forgot-password', [CustomerAuthController::class, 'forgotPassword'])->name('password.email');
        Route::post('/reset-password', [CustomerAuthController::class, 'resetPassword'])->name('password.update');
        Route::get('/email/verify/{id}/{hash}', [CustomerAuthController::class, 'verify'])
            ->middleware(['signed', 'throttle:6,1'])
            ->name('verification.verify');
        Route::get('/auth/{provider}/redirect', [CustomerAuthController::class, 'redirectToProvider'])->name('oauth.redirect');
        Route::get('/auth/{provider}/callback', [CustomerAuthController::class, 'handleProviderCallback'])->name('oauth.callback');

        Route::middleware('auth:sanctum')->group(function () {
            Route::post('/logout', [CustomerAuthController::class, 'logout'])->name('logout');
            Route::get('/me', [CustomerAuthController::class, 'me'])->name('me');
            Route::post('/email/verification-notification', [CustomerAuthController::class, 'resendVerification'])
                ->middleware('throttle:6,1')
                ->name('verification.send');
        });
    });

    // --- Staff Auth ---
    Route::prefix('staff')->name('staff.')->group(function () {
        Route::post('/register', [StaffAuthController::class, 'register'])->name('register');
        Route::post('/login', [StaffAuthController::class, 'login'])->name('login');
        Route::post('/forgot-password', [StaffAuthController::class, 'forgotPassword'])->name('password.email');
        Route::post('/reset-password', [StaffAuthController::class, 'resetPassword'])->name('password.update');
        Route::get('/email/verify/{id}/{hash}', [StaffAuthController::class, 'verify'])
            ->middleware(['signed', 'throttle:6,1'])
            ->name('verification.verify');

        Route::middleware('auth:sanctum')->group(function () {
            Route::post('/logout', [StaffAuthController::class, 'logout'])->name('logout');
            Route::get('/me', [StaffAuthController::class, 'me'])->name('me');
            Route::post('/email/verification-notification', [StaffAuthController::class, 'resendVerification'])
                ->middleware('throttle:6,1')
                ->name('verification.send');
        });
    });

    // --- Authenticated Features (Shared Staff & Customer) ---
    Route::middleware('auth:sanctum')->group(function () {
        // Notification Preferences
        Route::prefix('notifications/preferences')->name('notifications.preferences.')->group(function () {
            Route::get('/', [NotificationPreferenceController::class, 'index'])->name('index');
            Route::put('/', [NotificationPreferenceController::class, 'update'])->name('update');
        });
    });

    // --- HR Module (Protected Staff) ---
    Route::middleware('auth:sanctum')->prefix('hr')->name('hr.')->group(function () {

        Route::prefix('departments')->name('departments.')->group(function () {
            Route::get('/', [DepartmentController::class, 'index'])->name('index');
            Route::get('/{id}', [DepartmentController::class, 'show'])->name('show');
            Route::post('/', [DepartmentController::class, 'store'])->name('store');
            Route::put('/{id}', [DepartmentController::class, 'update'])->name('update');
            Route::delete('/{id}', [DepartmentController::class, 'destroy'])->name('destroy');
        });

        Route::prefix('employees')->name('employees.')->group(function () {
            Route::get('/', [EmployeeController::class, 'index'])->name('index');
            Route::get('/{id}', [EmployeeController::class, 'show'])->name('show');
            Route::post('/', [EmployeeController::class, 'store'])->name('store');
            Route::post('/{id}', [EmployeeController::class, 'update'])->name('update'); // POST for multipart form-data
            Route::delete('/{id}', [EmployeeController::class, 'destroy'])->name('destroy');
            Route::post('/{id}/terminate', [EmployeeController::class, 'terminate'])->name('terminate');
        });

        Route::prefix('employee-documents')->name('employee-documents.')->group(function () {
            Route::get('/', [EmployeeDocumentController::class, 'index'])->name('index');
            Route::post('/', [EmployeeDocumentController::class, 'store'])->name('store');
            Route::delete('/{id}', [EmployeeDocumentController::class, 'destroy'])->name('destroy');
        });

        Route::prefix('attendance')->name('attendance.')->group(function () {
            Route::get('/', [AttendanceController::class, 'index'])->name('index');
            Route::post('/check-in', [AttendanceController::class, 'checkIn'])->name('check-in');
            Route::post('/check-out', [AttendanceController::class, 'checkOut'])->name('check-out');
        });

        Route::prefix('positions')->name('positions.')->group(function () {
            Route::get('/', [PositionController::class, 'index'])->name('index');
            Route::get('/{id}', [PositionController::class, 'show'])->name('show');
            Route::post('/', [PositionController::class, 'store'])->name('store');
            Route::put('/{id}', [PositionController::class, 'update'])->name('update');
            Route::delete('/{id}', [PositionController::class, 'destroy'])->name('destroy');
        });

        Route::prefix('trainings')->name('trainings.')->group(function () {
            Route::get('/', [TrainingController::class, 'index'])->name('index');
            Route::get('/{id}', [TrainingController::class, 'show'])->name('show');
            Route::post('/', [TrainingController::class, 'store'])->name('store');
            Route::put('/{id}', [TrainingController::class, 'update'])->name('update');
            Route::delete('/{id}', [TrainingController::class, 'destroy'])->name('destroy');
            Route::post('/{id}/enroll', [TrainingController::class, 'enroll'])->name('enroll');
            Route::post('/{id}/complete', [TrainingController::class, 'complete'])->name('complete');
        });

        Route::prefix('performance')->name('performance.')->group(function () {
            Route::get('/reviews', [PerformanceController::class, 'reviews'])->name('reviews.index');
            Route::post('/reviews', [PerformanceController::class, 'storeReview'])->name('reviews.store');
            Route::get('/goals', [PerformanceController::class, 'goals'])->name('goals.index');
            Route::post('/goals', [PerformanceController::class, 'storeGoal'])->name('goals.store');
        });

        Route::prefix('payroll')->name('payroll.')->group(function () {
            Route::get('/', [PayrollController::class, 'index'])->name('index');
            Route::post('/generate', [PayrollController::class, 'generate'])->name('generate');
            Route::post('/{id}/mark-paid', [PayrollController::class, 'markPaid'])->name('mark-paid');
        });

        Route::prefix('leave')->name('leave.')->group(function () {
            Route::get('/', [LeaveController::class, 'index'])->name('index');
            Route::post('/', [LeaveController::class, 'store'])->name('store');
            Route::post('/{id}/approve', [LeaveController::class, 'approve'])->name('approve');
            Route::post('/{id}/reject', [LeaveController::class, 'reject'])->name('reject');
        });

        Route::prefix('job-postings')->name('job-postings.')->group(function () {
            Route::get('/', [JobPostingController::class, 'index'])->name('index');
            Route::get('/{id}', [JobPostingController::class, 'show'])->name('show');
            Route::post('/', [JobPostingController::class, 'store'])->name('store');
            Route::put('/{id}', [JobPostingController::class, 'update'])->name('update');
            Route::delete('/{id}', [JobPostingController::class, 'destroy'])->name('destroy');
        });

        Route::prefix('applicants')->name('applicants.')->group(function () {
            Route::get('/', [ApplicantController::class, 'index'])->name('index');
            Route::post('/', [ApplicantController::class, 'store'])->name('store');
            Route::patch('/{id}/move', [ApplicantController::class, 'move'])->name('move');
        });

        Route::prefix('interviews')->name('interviews.')->group(function () {
            Route::post('/schedule', [InterviewController::class, 'schedule'])->name('schedule');
        });

        // Notification Templates (Admin/HR Only)
        Route::prefix('notifications/templates')->name('notifications.templates.')->group(function () {
            Route::get('/', [NotificationTemplateController::class, 'index'])->name('index');
            Route::get('/{id}', [NotificationTemplateController::class, 'show'])->name('show');
            Route::post('/', [NotificationTemplateController::class, 'store'])->name('store');
            Route::put('/{id}', [NotificationTemplateController::class, 'update'])->name('update');
            Route::delete('/{id}', [NotificationTemplateController::class, 'destroy'])->name('destroy');
        });
    });

    // --- CMS Page Module ---
    Route::prefix('pages')->name('pages.')->group(function () {
        Route::get('/public/{slug}', [PageController::class, 'showPublic'])->name('show.public');

        Route::middleware('auth:sanctum')->group(function () {
            Route::get('/', [PageController::class, 'index'])->name('index');
            Route::get('/{id}', [PageController::class, 'show'])->name('show');
            Route::post('/', [PageController::class, 'store'])->name('store');
            Route::put('/{id}', [PageController::class, 'update'])->name('update');
            Route::delete('/{id}', [PageController::class, 'destroy'])->name('destroy');
            Route::post('/{id}/publish', [PageController::class, 'publish'])->name('publish');
        });
    });

    // --- Blog Module ---
    Route::prefix('blog')->name('blog.')->group(function () {
        Route::get('/posts', [BlogController::class, 'index'])->name('posts.index');
        Route::get('/posts/{slug}', [BlogController::class, 'show'])->name('posts.show');
        Route::get('/posts/{slug}/comments', [BlogController::class, 'getComments'])->name('posts.comments.index');
        Route::post('/posts/{slug}/comments', [BlogController::class, 'publicComment'])->name('posts.comments.public');
        Route::get('/categories', [BlogController::class, 'categoriesIndex'])->name('categories.index');

        Route::middleware('auth:sanctum')->group(function () {
            Route::post('/posts', [BlogController::class, 'store'])->name('posts.store');
            Route::put('/posts/{id}', [BlogController::class, 'update'])->name('posts.update');
            Route::delete('/posts/{id}', [BlogController::class, 'destroy'])->name('posts.destroy');

            Route::post('/categories', [BlogController::class, 'categoriesStore'])->name('categories.store');
            Route::put('/categories/{id}', [BlogController::class, 'categoriesUpdate'])->name('categories.update');
            Route::delete('/categories/{id}', [BlogController::class, 'categoriesDestroy'])->name('categories.destroy');

            Route::put('/comments/{id}', [BlogController::class, 'updateComment'])->name('comments.update');
            Route::delete('/comments/{id}', [BlogController::class, 'destroyComment'])->name('comments.destroy');
        });
    });
});
