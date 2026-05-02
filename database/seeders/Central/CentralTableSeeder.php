<?php

namespace Database\Seeders\Central;

use App\Models\Central\Plan;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use InvalidArgumentException;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class CentralTableSeeder extends Seeder
{
    /**
     * Run the central roles, permissions, settings, and notifications seeder.
     */
    public function run(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $this->seedPlans();
        $this->seedRolesAndPermissions();
        $this->seedSettings();
        $this->seedPages();
        $this->seedBlogCategories();
        $this->seedNotifications();
    }

    /**
     * Seed central platform plans.
     */
    private function seedPlans(): void
    {
        Plan::query()->updateOrCreate(
            ['slug' => 'starter'],
            [
                'name' => 'Starter',
                'description' => 'Launch your first storefront with core ecommerce features.',
                'price_cents' => 0,
                'currency' => 'USD',
                'interval' => 'month',
                'is_active' => true,
                'features' => [
                    'Up to 50 products',
                    'Standard checkout',
                    'Email support',
                ],
                'limits' => [
                    'max_products' => 50,
                    'allows_custom_domain' => false,
                ],
            ]
        );

        Plan::query()->updateOrCreate(
            ['slug' => 'growth'],
            [
                'name' => 'Growth',
                'description' => 'Scale catalog and branding as you grow.',
                'price_cents' => 2900,
                'currency' => 'USD',
                'interval' => 'month',
                'is_active' => true,
                'features' => [
                    'Up to 1,000 products',
                    'Custom domain',
                    'Priority support',
                ],
                'limits' => [
                    'max_products' => 1000,
                    'allows_custom_domain' => true,
                ],
            ]
        );

        Plan::query()->updateOrCreate(
            ['slug' => 'scale'],
            [
                'name' => 'Scale',
                'description' => 'High-volume commerce with generous limits.',
                'price_cents' => 9900,
                'currency' => 'USD',
                'interval' => 'month',
                'is_active' => true,
                'features' => [
                    'Up to 100,000 products',
                    'Custom domain',
                    'Dedicated support',
                ],
                'limits' => [
                    'max_products' => 100000,
                    'allows_custom_domain' => true,
                ],
            ]
        );
    }

    /**
     * Seed central roles and permissions based on config/roles.php.
     */
    private function seedRolesAndPermissions(): void
    {
        $matrix = config('roles.central');
        $guards = config('roles.guards', ['web']); // Updated to web only

        if (! is_array($matrix)) {
            throw new InvalidArgumentException('The config value [roles.central] must be an array. Check config/roles.php.');
        }

        if (! is_array($guards) || $guards === []) {
            throw new InvalidArgumentException('The config value [roles.guards] must be a non-empty array. Check config/roles.php.');
        }

        // Collect every concrete permission referenced by any role
        $allPerms = collect($matrix)->flatten()->unique()->reject(fn ($p) => $p === '*');

        // Expand patterns like "tenants.*" → "tenants.view","tenants.create","tenants.update","tenants.delete"
        $expanded = $allPerms->flatMap(function ($p) {
            return str_ends_with($p, '.*')
                ? collect(['view', 'create', 'update', 'delete'])->map(fn ($a) => str_replace('*', $a, $p))
                : [$p];
        })->unique()->values();

        foreach ($guards as $guard) {
            foreach ($expanded as $perm) {
                Permission::query()->firstOrCreate([
                    'name' => $perm,
                    'guard_name' => $guard,
                ]);
            }

            foreach ($matrix as $roleName => $perms) {
                if (! is_array($perms)) {
                    throw new InvalidArgumentException("Permissions for role [{$roleName}] must be an array.");
                }

                $role = Role::query()->firstOrCreate([
                    'name' => $roleName,
                    'guard_name' => $guard,
                ]);

                if (in_array('*', $perms, true)) {
                    $role->syncPermissions(
                        Permission::query()
                            ->where('guard_name', $guard)
                            ->get()
                    );
                } else {
                    $needed = collect($perms)
                        ->flatMap(fn ($p) => str_ends_with($p, '.*')
                            ? collect(['view', 'create', 'update', 'delete'])->map(fn ($a) => str_replace('*', $a, $p))
                            : [$p])
                        ->unique()
                        ->values();

                    $role->syncPermissions(
                        Permission::query()
                            ->whereIn('name', $needed)
                            ->where('guard_name', $guard)
                            ->get()
                    );
                }
            }
        }
    }

    /**
     * Seed central platform settings.
     */
    private function seedSettings(): void
    {
        DB::table('settings')->updateOrInsert(
            ['id' => 1],
            [
                'name' => 'Central Administration',
                'tagline' => 'Your trusted management platform',
                'currency' => 'USD',
                'timezone' => 'UTC',
                'language' => 'en',
                'logo_path' => null,
                'favicon_path' => null,
                'primary_color' => '#2563eb',
                'social' => json_encode([
                    'facebook' => null,
                    'instagram' => null,
                    'twitter' => null,
                    'linkedin' => null,
                ]),
                'payment_providers' => json_encode([
                    'stripe' => [
                        'enabled' => false,
                        'test_mode' => true,
                        'test' => [
                            'public_key' => 'pk_test_stripe_...',
                            'secret_key' => 'sk_test_stripe_...',
                            'webhook_secret' => 'whsec_test_stripe_...',
                        ],
                        'live' => [
                            'public_key' => 'pk_live_stripe_...',
                            'secret_key' => 'sk_live_stripe_...',
                            'webhook_secret' => 'whsec_live_stripe_...',
                        ],
                    ],
                    'paypal' => [
                        'enabled' => false,
                        'test_mode' => true,
                        'test' => [
                            'client_id' => 'client_test_paypal_...',
                            'secret' => 'secret_test_paypal_...',
                        ],
                        'live' => [
                            'client_id' => 'client_live_paypal_...',
                            'secret' => 'secret_live_paypal_...',
                        ],
                    ],
                ]),
                'contact_email' => null,
                'contact_phone' => null,
                'address' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );
    }

    /**
     * Seed central platform default pages.
     */
    private function seedPages(): void
    {
        $pages = [
            [
                'title' => 'About Us',
                'slug' => 'about-us',
                'content' => 'Write information about your platform, brand, mission, and values here.',
                'seo_title' => 'About Us',
                'seo_description' => 'Learn more about our platform.',
                'is_published' => true,
            ],
            [
                'title' => 'Contact Us',
                'slug' => 'contact-us',
                'content' => 'Add your contact details, support email, phone number, and address here.',
                'seo_title' => 'Contact Us',
                'seo_description' => 'Contact our platform for help and enquiries.',
                'is_published' => true,
            ],
            [
                'title' => 'Privacy Policy',
                'slug' => 'privacy-policy',
                'content' => 'Add your privacy policy content here.',
                'seo_title' => 'Privacy Policy',
                'seo_description' => 'Read our privacy policy.',
                'is_published' => true,
            ],
            [
                'title' => 'Terms and Conditions',
                'slug' => 'terms-and-conditions',
                'content' => 'Add your terms and conditions content here.',
                'seo_title' => 'Terms and Conditions',
                'seo_description' => 'Read our terms and conditions.',
                'is_published' => true,
            ],
        ];

        foreach ($pages as $page) {
            DB::table('pages')->updateOrInsert(
                ['slug' => $page['slug']],
                [
                    'title' => $page['title'],
                    'content' => $page['content'],
                    'blocks' => null,
                    'seo_title' => $page['seo_title'],
                    'seo_description' => $page['seo_description'],
                    'is_published' => $page['is_published'],
                    'published_at' => $page['is_published'] ? now() : null,
                    'created_at' => now(),
                    'updated_at' => now(),
                    'deleted_at' => null,
                ]
            );
        }
    }

    /**
     * Seed central platform blog categories.
     */
    private function seedBlogCategories(): void
    {
        $categories = [
            [
                'name' => 'News',
                'description' => 'Platform news, announcements, and updates.',
            ],
            [
                'name' => 'Guides',
                'description' => 'Helpful guides and tutorials.',
            ],
            [
                'name' => 'Feature Updates',
                'description' => 'Updates about new features.',
            ],
        ];

        foreach ($categories as $category) {
            DB::table('blog_categories')->updateOrInsert(
                ['slug' => Str::slug($category['name'])],
                [
                    'name' => $category['name'],
                    'description' => $category['description'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }
    }

    /**
     * Seed central platform notification channels and templates.
     */
    private function seedNotifications(): void
    {
        // 1. Seed Notification Channels
        $channels = [
            ['key' => 'email', 'label' => 'Email Notifications', 'is_active' => true],
            ['key' => 'in_app', 'label' => 'In-App Notifications', 'is_active' => true],
            ['key' => 'sms', 'label' => 'SMS Notifications', 'is_active' => false],
        ];

        foreach ($channels as $channel) {
            DB::table('notification_channels')->updateOrInsert(
                ['key' => $channel['key']],
                [
                    'label' => $channel['label'],
                    'is_active' => $channel['is_active'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }

        // 2. Seed Default Notification Templates
        $templates = [
            // --- Tenant Lifecycle ---
            [
                'event' => 'tenant_registered',
                'channel' => 'email',
                'subject' => 'Welcome to our Platform!',
                'body' => "Hello {tenant_name},\n\nWelcome to our platform! Your tenant account has been created successfully.\n\nYou can log in to your portal and set up your store using the following details:\nDomain: {domain}\nEmail: {email}\nPassword: {password}\n\nBest regards,\nThe Platform Team",
                'is_active' => true,
            ],
            [
                'event' => 'tenant_subscribed',
                'channel' => 'email',
                'subject' => 'Subscription Confirmed',
                'body' => "Hello {tenant_name},\n\nThank you for subscribing to the {plan_name} plan! Your subscription is now active.\n\nBest regards,\nThe Platform Team",
                'is_active' => true,
            ],
            [
                'event' => 'tenant_canceled',
                'channel' => 'email',
                'subject' => 'Subscription Canceled',
                'body' => "Hello {tenant_name},\n\nYour subscription to the {plan_name} plan has been successfully canceled.\n\nBest regards,\nThe Platform Team",
                'is_active' => true,
            ],
            [
                'event' => 'plan_expiring',
                'channel' => 'email',
                'subject' => 'Action Required: Your plan is expiring soon',
                'body' => "Hello {tenant_name},\n\nYour subscription to the {plan_name} plan is scheduled to expire on {expiration_date}. Please renew your subscription to ensure uninterrupted access to your store.\n\nBest regards,\nThe Platform Team",
                'is_active' => true,
            ],

            // --- Authentication & Account ---
            [
                'event' => 'password_reset',
                'channel' => 'email',
                'subject' => 'Password Reset Request',
                'body' => "Hello {name},\n\nWe received a request to reset your password. Please use the following link to reset it: \n\n**{url}**\n\nIf you did not request this, please ignore this email.",
                'is_active' => true,
            ],
        ];

        foreach ($templates as $template) {
            DB::table('notification_templates')->updateOrInsert(
                [
                    'event' => $template['event'],
                    'channel' => $template['channel'],
                ],
                [
                    'subject' => $template['subject'],
                    'body' => $template['body'],
                    'greeting' => $template['greeting'] ?? 'Hello,',
                    'closing' => $template['closing'] ?? 'Best regards,',
                    'sign_off' => $template['sign_off'] ?? config('app.name'),
                    'logo_url' => $template['logo_url'] ?? null,
                    'logo_alt' => $template['logo_alt'] ?? 'Logo',
                    'header_bg_color' => $template['header_bg_color'] ?? '#1e2b2e',
                    'accent_color' => $template['accent_color'] ?? '#73bc1c',
                    'is_active' => $template['is_active'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }
    }
}
