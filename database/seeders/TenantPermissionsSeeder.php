<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class TenantPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // Define Tenant Permissions
        $permissions = [
            // Users (Tenant - Staff/Admin)
            'view tenant users', 'create tenant users', 'update tenant users', 'delete tenant users', 'assign tenant roles',
            // Customers
            'view customers', 'create customers', 'update customers', 'delete customers',
            // Products
            'view products', 'create products', 'update products', 'delete products', 'manage product inventory',
            // Categories
            'view categories', 'create categories', 'update categories', 'delete categories',
            // Orders
            'view orders', 'update orders', 'delete orders', 'manage order shipments',
            // Shipments
            'view shipments', 'create shipments', 'update shipments', 'delete shipments',
            // Payments
            'view payments', 'process payments', 'refund payments',
            // Coupons
            'view coupons', 'create coupons', 'update coupons', 'delete coupons',
            // Reviews
            'view reviews', 'manage reviews',
            // Addresses
            'view addresses', 'create addresses', 'update addresses', 'delete addresses',
            // Wishlists
            'view wishlists', 'manage wishlists',
            // Cart
            'manage cart', // Added cart permission
            // Settings (Tenant)
            'view tenant settings', 'update tenant settings',
            // Mail Settings
            'view mail settings', 'update mail settings',
            // Inventory Movements
            'view inventory movements', 'create inventory movements',
            // Notification Channels (Tenant)
            'view tenant notification channels', 'create tenant notification channels', 'update tenant notification channels', 'delete tenant notification channels',
            // Notification Templates (Tenant)
            'view tenant notification templates', 'create tenant notification templates', 'update tenant notification templates', 'delete tenant notification templates',
            // Notification Preferences (Tenant)
            'view tenant notification preferences', 'update tenant notification preferences',

            // HR Module
            'view departments', 'create departments', 'update departments', 'delete departments',
            'view positions', 'create positions', 'update positions', 'delete positions',
            'view employees', 'create employees', 'update employees', 'delete employees', 'manage employee documents', 'manage employee goals', 'manage employee attendance', 'manage employee leave requests', 'manage employee payslips', 'manage employee performance reviews', 'assign employee roles',
            'view applicants', 'create applicants', 'update applicants', 'delete applicants', 'manage applicant interviews',
            'view job postings', 'create job postings', 'update job postings', 'delete job postings',
            'view interviews', 'create interviews', 'update interviews', 'delete interviews',
            'view attendances', 'create attendances', 'update attendances', 'delete attendances',
            'view employee documents', 'upload employee documents', 'delete employee documents',
            'view goals', 'create goals', 'update goals', 'delete goals',
            'view leave requests', 'create leave requests', 'update leave requests', 'approve leave requests', 'reject leave requests', 'delete leave requests',
            'view payslips', 'create payslips', 'update payslips', 'delete payslips',
            'view performance reviews', 'create performance reviews', 'update performance reviews', 'delete performance reviews',
            'view trainings', 'create trainings', 'update trainings', 'delete trainings', 'assign trainings to employees',

            // CMS Module
            'view pages', 'create pages', 'update pages', 'delete pages', 'publish pages',
            'view blog categories', 'create blog categories', 'update blog categories', 'delete blog categories',
            'view blog posts', 'create blog posts', 'update blog posts', 'delete blog posts', 'publish blog posts',
            'view blog comments', 'approve blog comments', 'delete blog comments',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'sanctum']);
        }

        // Create Roles and Assign Permissions
        $adminRole = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'sanctum']);
        $staffRole = Role::firstOrCreate(['name' => 'staff', 'guard_name' => 'sanctum']);
        $customerRole = Role::firstOrCreate(['name' => 'customer', 'guard_name' => 'sanctum']);

        // Admin gets all tenant permissions
        $adminRole->givePermissionTo(Permission::where('guard_name', 'sanctum')->get());

        // Staff gets a subset of permissions (example)
        $staffRole->givePermissionTo([
            'view products', 'create products', 'update products', 'manage product inventory',
            'view categories',
            'view orders', 'update orders', 'manage order shipments',
            'view customers', 'update customers',
            'view reviews', 'manage reviews',
            'view employees', 'view attendances', 'view leave requests',
            'view pages', 'view blog posts',
        ]);

        // Customer gets basic permissions
        $customerRole->givePermissionTo([
            'view products', 'view categories', 'view orders', 'view addresses', 'create addresses', 'update addresses', 'delete addresses', 'view wishlists', 'manage wishlists', 'view reviews', 'create reviews', 'manage cart', // Assigned cart permission
        ]);

        // You might want to assign default roles to existing tenant users here
        // Example: App\Models\Tenant\User::whereDoesntHave('roles')->where('user_type', 'admin')->first()?->assignRole('admin');
        // Example: App\Models\Tenant\User::whereDoesntHave('roles')->where('user_type', 'customer')->first()?->assignRole('customer');
    }
}
