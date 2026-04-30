<?php

namespace App\Services\Tenant;

use App\Models\Tenant\Wishlist;
use Illuminate\Database\Eloquent\Collection;

/**
 * Class WishlistService
 * * Handles business logic related to tenant wishlists.
 */
class WishlistService
{
    /**
     * Retrieve the active wishlist for a specific customer.
     */
    public function getCustomerWishlist(int $customerId): Collection
    {
        return Wishlist::query()
            ->with('product')
            ->where('customer_id', $customerId)
            ->get();
    }

    /**
     * Toggle a product on a customer's wishlist.
     * Removes the product if it exists; adds it if it does not.
     *
     * @param  array  $data  Validated wishlist toggle data.
     */
    public function toggleWishlistItem(int $customerId, array $data): array
    {
        $existing = Wishlist::query()
            ->where('customer_id', $customerId)
            ->where('product_id', $data['product_id'])
            ->first();

        if ($existing) {
            $existing->delete();

            return ['wishlisted' => false];
        }

        Wishlist::query()->create([
            'customer_id' => $customerId,
            'product_id' => $data['product_id'],
        ]);

        return ['wishlisted' => true];
    }
}
