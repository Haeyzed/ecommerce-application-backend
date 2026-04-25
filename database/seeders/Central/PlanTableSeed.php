<?php

namespace Database\Seeders\Central;

use App\Models\Central\Plan;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PlanTableSeed extends Seeder
{
    use WithoutModelEvents;

    /**
     * Run the database seeds.
     */
    public function run(): void
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
}
