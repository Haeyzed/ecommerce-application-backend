<?php

namespace App\Console\Commands\Tenant;

use App\Services\Tenant\CartService;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('app:clean-expired-carts')]
#[Description('Deletes abandoned carts older than 7 days for the active tenant.')]
class CleanExpiredCarts extends Command
{
    /**
     * Execute the console command.
     */
    public function handle(CartService $cartService): int
    {
        $deletedCount = $cartService->deleteExpiredCarts();

        $this->info("Deleted {$deletedCount} expired carts for tenant: ".tenant('id'));

        return self::SUCCESS;
    }
}
