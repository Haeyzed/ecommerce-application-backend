<?php

namespace Database\Seeders\Central;

use App\Models\Central\Plan;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PlanTableSeed extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Plan::query()->updateOrCreate(['slug' => 'starter'], [
            'name' => 'Starter', 'price_cents' => 0,
            'max_products' => 50, 'allows_custom_domain' => false,
        ]);
        Plan::query()->updateOrCreate(['slug' => 'growth'], [
            'name' => 'Growth', 'price_cents' => 2900,
            'max_products' => 1000, 'allows_custom_domain' => true,
        ]);
        Plan::query()->updateOrCreate(['slug' => 'scale'], [
            'name' => 'Scale', 'price_cents' => 9900,
            'max_products' => 100000, 'allows_custom_domain' => true,
        ]);
    }
}
