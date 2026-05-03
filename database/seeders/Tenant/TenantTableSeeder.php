<?php

namespace Database\Seeders\Tenant;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use InvalidArgumentException;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class TenantTableSeeder extends Seeder
{
    public function run(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

//        $this->seedRolesAndPermissions();
        $this->seedSettings();
        $this->seedMailSettings();
        $this->seedPages();
        $this->seedBlogCategories();
        $this->seedDepartmentsAndPositions();
        $this->seedNotifications();
    }

    private function seedRolesAndPermissions(): void
    {
        $matrix = config('roles.tenant');
        $guards = config('roles.guards', ['web', 'sanctum']); // Added sanctum just in case

        if (! is_array($matrix)) {
            throw new InvalidArgumentException('The config value [roles.tenant] must be an array. Check config/roles.php.');
        }

        if (! is_array($guards) || $guards === []) {
            throw new InvalidArgumentException('The config value [roles.guards] must be a non-empty array. Check config/roles.php.');
        }

        $allPerms = collect($matrix)
            ->flatten()
            ->unique()
            ->reject(fn ($permission) => $permission === '*');

        $expanded = $allPerms
            ->flatMap(fn ($permission) => str_ends_with($permission, '.*')
                ? collect(['view', 'create', 'update', 'delete'])
                    ->map(fn ($action) => str_replace('*', $action, $permission))
                : [$permission])
            ->unique()
            ->values();

        foreach ($guards as $guard) {
            foreach ($expanded as $permission) {
                Permission::query()->firstOrCreate([
                    'name' => $permission,
                    'guard_name' => $guard,
                ]);
            }

            foreach ($matrix as $roleName => $permissions) {
                if (! is_array($permissions)) {
                    throw new InvalidArgumentException("Permissions for tenant role [{$roleName}] must be an array.");
                }

                $role = Role::query()->firstOrCreate([
                    'name' => $roleName,
                    'guard_name' => $guard,
                ]);

                if (in_array('*', $permissions, true)) {
                    $role->syncPermissions(
                        Permission::query()
                            ->where('guard_name', $guard)
                            ->get()
                    );

                    continue;
                }

                $needed = collect($permissions)
                    ->flatMap(fn ($permission) => str_ends_with($permission, '.*')
                        ? collect(['view', 'create', 'update', 'delete'])
                            ->map(fn ($action) => str_replace('*', $action, $permission))
                        : [$permission])
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

    private function seedSettings(): void
    {
        DB::table('settings')->updateOrInsert(
            ['id' => 1],
            [
                'name' => 'Default Store',
                'tagline' => 'Your trusted online store',
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
                    'paystack' => [
                        'enabled' => false,
                        'test_mode' => true,
                        'test' => [
                            'public_key' => 'pk_test_paystack_...',
                            'secret_key' => 'sk_test_paystack_...',
                        ],
                        'live' => [
                            'public_key' => 'pk_live_paystack_...',
                            'secret_key' => 'sk_live_paystack_...',
                        ],
                    ],
                    'flutterwave' => [
                        'enabled' => false,
                        'test_mode' => true,
                        'test' => [
                            'public_key' => 'flwpubk_test_...',
                            'secret_key' => 'flwsec_test_...',
                            'encryption_key' => 'flw_enc_test_...',
                        ],
                        'live' => [
                            'public_key' => 'flwpubk_live_...',
                            'secret_key' => 'flwsec_live_...',
                            'encryption_key' => 'flw_enc_live_...',
                        ],
                    ],
                    'razorpay' => [
                        'enabled' => false,
                        'test_mode' => true,
                        'test' => [
                            'key_id' => 'rzp_test_...',
                            'key_secret' => 'rzp_test_secret_...',
                        ],
                        'live' => [
                            'key_id' => 'rzp_live_...',
                            'key_secret' => 'rzp_live_secret_...',
                        ],
                    ],
                    'square' => [
                        'enabled' => false,
                        'test_mode' => true,
                        'test' => [
                            'application_id' => 'sandbox-sq0idb_...',
                            'access_token' => 'sandbox-sq0atb_...',
                            'location_id' => 'sandbox_location_...',
                        ],
                        'live' => [
                            'application_id' => 'sq0idp_...',
                            'access_token' => 'sq0atp_...',
                            'location_id' => 'live_location_...',
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

    private function seedMailSettings(): void
    {
        DB::table('mail_settings')->updateOrInsert(
            ['id' => 1],
            [
                'mailer' => 'smtp',
                'host' => '127.0.0.1',
                'port' => 1025,
                'username' => null,
                'password' => null,
                'encryption' => 'tls',
                'from_address' => 'noreply@yourstore.com',
                'from_name' => 'Default Store',
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );
    }

    private function seedPages(): void
    {
        $pages = [
            [
                'title' => 'About Us',
                'slug' => 'about-us',
                'content' => 'Write information about your store, brand, mission, and values here.',
                'seo_title' => 'About Us',
                'seo_description' => 'Learn more about our store.',
                'is_published' => true,
            ],
            [
                'title' => 'Contact Us',
                'slug' => 'contact-us',
                'content' => 'Add your contact details, support email, phone number, and address here.',
                'seo_title' => 'Contact Us',
                'seo_description' => 'Contact our store for help and enquiries.',
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
            [
                'title' => 'Shipping Policy',
                'slug' => 'shipping-policy',
                'content' => 'Add your shipping policy content here.',
                'seo_title' => 'Shipping Policy',
                'seo_description' => 'Read our shipping policy.',
                'is_published' => true,
            ],
            [
                'title' => 'Refund Policy',
                'slug' => 'refund-policy',
                'content' => 'Add your refund policy content here.',
                'seo_title' => 'Refund Policy',
                'seo_description' => 'Read our refund policy.',
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

    private function seedBlogCategories(): void
    {
        $categories = [
            [
                'name' => 'News',
                'description' => 'Store news, announcements, and updates.',
            ],
            [
                'name' => 'Guides',
                'description' => 'Helpful guides, tutorials, and shopping tips.',
            ],
            [
                'name' => 'Product Updates',
                'description' => 'Updates about products, collections, and features.',
            ],
            [
                'name' => 'Promotions',
                'description' => 'Sales, offers, discounts, and promotional updates.',
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

    private function seedDepartmentsAndPositions(): void
    {
        $departments = [
            'Management' => [
                'code' => 'MGT',
                'positions' => [
                    [
                        'title' => 'Store Owner',
                        'min_salary_cents' => null,
                        'max_salary_cents' => null,
                    ],
                    [
                        'title' => 'Store Manager',
                        'min_salary_cents' => 300000,
                        'max_salary_cents' => 700000,
                    ],
                ],
            ],
            'Sales' => [
                'code' => 'SAL',
                'positions' => [
                    [
                        'title' => 'Sales Representative',
                        'min_salary_cents' => 150000,
                        'max_salary_cents' => 350000,
                    ],
                    [
                        'title' => 'Sales Manager',
                        'min_salary_cents' => 300000,
                        'max_salary_cents' => 600000,
                    ],
                ],
            ],
            'Customer Support' => [
                'code' => 'SUP',
                'positions' => [
                    [
                        'title' => 'Support Agent',
                        'min_salary_cents' => 150000,
                        'max_salary_cents' => 350000,
                    ],
                    [
                        'title' => 'Support Lead',
                        'min_salary_cents' => 250000,
                        'max_salary_cents' => 500000,
                    ],
                ],
            ],
            'Operations' => [
                'code' => 'OPS',
                'positions' => [
                    [
                        'title' => 'Operations Officer',
                        'min_salary_cents' => 200000,
                        'max_salary_cents' => 450000,
                    ],
                    [
                        'title' => 'Inventory Officer',
                        'min_salary_cents' => 180000,
                        'max_salary_cents' => 400000,
                    ],
                ],
            ],
            'Marketing' => [
                'code' => 'MKT',
                'positions' => [
                    [
                        'title' => 'Marketing Officer',
                        'min_salary_cents' => 180000,
                        'max_salary_cents' => 450000,
                    ],
                    [
                        'title' => 'Content Manager',
                        'min_salary_cents' => 200000,
                        'max_salary_cents' => 500000,
                    ],
                ],
            ],
        ];

        foreach ($departments as $departmentName => $departmentData) {
            $department = DB::table('departments')->where('name', $departmentName)->first();

            if (! $department) {
                $departmentId = DB::table('departments')->insertGetId([
                    'name' => $departmentName,
                    'code' => $departmentData['code'],
                    'parent_id' => null,
                    'manager_employee_id' => null,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            } else {
                $departmentId = $department->id;

                DB::table('departments')
                    ->where('id', $departmentId)
                    ->update([
                        'code' => $departmentData['code'],
                        'updated_at' => now(),
                    ]);
            }

            foreach ($departmentData['positions'] as $position) {
                DB::table('positions')->updateOrInsert(
                    [
                        'department_id' => $departmentId,
                        'title' => $position['title'],
                    ],
                    [
                        'min_salary_cents' => $position['min_salary_cents'],
                        'max_salary_cents' => $position['max_salary_cents'],
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]
                );
            }
        }
    }

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
            // --- Authentication & Account ---
            [
                'event' => 'customer_registered',
                'channel' => 'email',
                'subject' => 'Welcome to {store_name}!',
                'body' => "Hello {name},\n\nWelcome to {store_name}! Your customer account has been created successfully. You can now log in and start exploring.\n\nBest regards,\nThe {store_name} Team",
                'is_active' => true,
            ],
            [
                'event' => 'admin_registered',
                'channel' => 'email',
                'subject' => 'Welcome to the Team!',
                'body' => "Hello {name},\n\nYour admin account at {store_name} has been set up by the administrator.\n\nPlease log in to the admin portal using the following credentials:\nEmail: {email}\nPassword: {password}\n\nFor security reasons, we recommend changing your password after your first login.\n\nWelcome aboard!",
                'is_active' => true,
            ],
            [
                'event' => 'password_reset',
                'channel' => 'email',
                'subject' => 'Password Reset Request',
                'body' => "Hello {name},\n\nWe received a request to reset your password. Please use the following security token to reset it: \n\n**{token}**\n\nIf you did not request this, please ignore this email.",
                'is_active' => true,
            ],

            // --- Billing & Invoices ---
            [
                'event' => 'invoice_created',
                'channel' => 'email',
                'subject' => 'New Invoice Available: {invoice_id}',
                'body' => "Hello {name},\n\nA new invoice ({invoice_id}) has been generated for your account. The total amount due is {amount} {currency}. Please ensure payment is made by {due_date}.\n\nThank you!",
                'is_active' => true,
            ],
            [
                'event' => 'invoice_paid',
                'channel' => 'email',
                'subject' => 'Payment Confirmation: Invoice {invoice_id}',
                'body' => "Hello {name},\n\nThank you! We have successfully received your payment of {amount} {currency} for invoice {invoice_id}.",
                'is_active' => true,
            ],

            // --- HR & Employee Management ---
            [
                'event' => 'leave_approved',
                'channel' => 'email',
                'subject' => 'Leave Request Approved',
                'body' => "Hello {name},\n\nYour leave request from {start_date} to {end_date} has been officially approved by {approver_name}.",
                'is_active' => true,
            ],
            [
                'event' => 'leave_rejected',
                'channel' => 'email',
                'subject' => 'Leave Request Update',
                'body' => "Hello {name},\n\nYour recent leave request has been reviewed but unfortunately could not be approved at this time.\nReason: {reason}",
                'is_active' => true,
            ],
            [
                'event' => 'payslip_generated',
                'channel' => 'email',
                'subject' => 'Your New Payslip is Available',
                'body' => "Hello {name},\n\nYour payslip for the period {period_start} to {period_end} has been generated. Your net pay is {net_amount} {currency}. You can view the full breakdown in your employee portal.",
                'is_active' => true,
            ],
            [
                'event' => 'interview_scheduled',
                'channel' => 'email',
                'subject' => 'Interview Scheduled: {job_title}',
                'body' => "Hello {name},\n\nWe would like to invite you for an interview for the {job_title} position on {scheduled_at}. The interview will be conducted via {mode}.\n\nLooking forward to speaking with you!",
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
