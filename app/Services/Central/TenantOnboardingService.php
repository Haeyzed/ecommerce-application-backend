<?php

namespace App\Services\Central;

use App\Enums\Tenant\RoleEnum;
use App\Models\Central\Plan;
use App\Models\Central\Tenant;
use App\Models\Tenant\Setting;
use App\Models\Tenant\Staff;
use App\Models\Tenant\User;
use App\Notifications\Central\DynamicTemplateNotification;
use Database\Seeders\Tenant\TenantTableSeeder;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Str;
use Throwable;

/**
 * Class TenantOnboardingService
 *
 * Handles end-to-end tenant onboarding:
 * 1. Creates Tenant record.
 * 2. Attaches the default `<subdomain>.<central_domain>` domain.
 * 3. Inside the tenant connection: seeds roles, creates owner user, creates staff profile, writes settings.
 * 4. Starts a trial subscription on the chosen plan.
 */
class TenantOnboardingService
{
    /**
     * Create a new TenantOnboardingService instance.
     */
    public function __construct(
        private readonly TenantSubscriptionService $subscriptionService
    ) {}

    /**
     * Onboard a new tenant.
     *
     * @param  array  $payload  Validated onboarding data.
     *
     * @throws Throwable
     */
    public function onboard(array $payload): Tenant
    {
        return DB::transaction(function () use ($payload) {
            $subdomain = Str::slug($payload['subdomain']);
            $centralDomain = parse_url((string) config('app.url'), PHP_URL_HOST)
                ?: 'ecommerce-application-backend.test';

            $rawPassword = $payload['owner_password'] ?? Str::password(8);

            /** @var Tenant $tenant */
            $tenant = Tenant::query()->create([
                'id' => $subdomain,
                'name' => $payload['name'],
                'owner_email' => $payload['owner_email'] ?? null,
                'plan_id' => $payload['plan_id'] ?? null,
                'data' => [
                    'currency' => $payload['currency'] ?? 'USD',
                    'timezone' => $payload['timezone'] ?? 'UTC',
                    'language' => $payload['language'] ?? 'en',
                ],
            ]);

            $tenantDomain = "{$subdomain}.{$centralDomain}";

            $tenant->domains()->create([
                'domain' => $tenantDomain,
            ]);

            /*
             * Boot tenant context and seed roles + owner inside the tenant DB.
             */
            $tenant->run(function () use ($payload, $subdomain, $rawPassword) {
                Artisan::call('db:seed', [
                    '--class' => TenantTableSeeder::class,
                    '--force' => true,
                ]);

                /** @var User $ownerUser */
                $ownerUser = User::query()->create([
                    'name' => $payload['owner_name'] ?? 'Owner',
                    'email' => $payload['owner_email'] ?? "owner@{$subdomain}.local",
                    'password' => $rawPassword,
                    'is_active' => true,
                ]);

                $ownerUser->assignRole(RoleEnum::OWNER->value);

                Staff::query()->create([
                    'user_id' => $ownerUser->id,
                    'currency' => $payload['currency'] ?? 'USD',
                    'locale' => $payload['language'] ?? 'en',
                    'is_active' => true,
                    'notes' => 'Tenant owner profile created during onboarding.',
                ]);

                Setting::query()->updateOrCreate(
                    ['id' => 1],
                    [
                        'currency' => $payload['currency'] ?? 'USD',
                        'timezone' => $payload['timezone'] ?? 'UTC',
                        'language' => $payload['language'] ?? 'en',
                        'name' => $payload['name'],
                        'tagline' => $payload['tagline'] ?? null,
                    ]
                );
            });

            /*
             * Kick off trial subscription if a plan was provided.
             */
            if (! empty($payload['plan_id'])) {
                $plan = Plan::query()->findOrFail($payload['plan_id']);

                $this->subscriptionService->startTrial(
                    $tenant,
                    $plan,
                    (int) ($payload['trial_days'] ?? 14)
                );
            }

            // Notify the owner of the new tenant
            if (! empty($payload['owner_email'])) {
                Notification::route('mail', $payload['owner_email'])
                    ->notify(new DynamicTemplateNotification(
                        event: 'tenant_registered',
                        templateData: [
                            'tenant_name' => $tenant->name,
                            'domain' => $tenantDomain,
                            'email' => $payload['owner_email'],
                            'password' => $rawPassword,
                        ]
                    ));
            }

            return $tenant->fresh('domains');
        });
    }
}
