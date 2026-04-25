<?php

namespace Database\Seeders\Tenant;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use InvalidArgumentException;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class TenantBootstrapSeeder extends Seeder
{
    public function run(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $this->seedRolesAndPermissions();
        $this->seedSettings();
        $this->seedPages();
        $this->seedBlogCategories();
        $this->seedDepartmentsAndPositions();
    }

    private function seedRolesAndPermissions(): void
    {
        $matrix = config('roles.tenant');
        $guards = config('roles.guards', ['web', 'sanctum']);

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
        if (DB::table('settings')->exists()) {
            return;
        }

        DB::table('settings')->insert([
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
                'stripe' => false,
                'paypal' => false,
                'paystack' => false,
            ]),
            'contact_email' => null,
            'contact_phone' => null,
            'address' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
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
}
