<?php

namespace App\Services\Tenant;

use App\Events\Tenant\CartItemAdded;
use App\Models\Tenant\Cart;
use App\Models\Tenant\CartItem;
use App\Models\Tenant\Coupon;
use App\Models\Tenant\Product;
use Illuminate\Support\Facades\DB;
use Throwable;

/**
 * Class CartService
 * * Handles business logic related to tenant shopping carts.
 */
class CartService
{
    /**
     * Resolve the active cart for a customer or guest session.
     * * Retrieves an existing cart or creates a new one if none exists.
     */
    public function resolveCart(?int $customerId, ?string $sessionToken): Cart
    {
        $cart = Cart::query()
            ->when($customerId, fn ($q) => $q->where('customer_id', $customerId))
            ->when(! $customerId && $sessionToken, fn ($q) => $q->where('session_token', $sessionToken))
            ->first();

        if (! $cart) {
            $cart = Cart::query()->create([
                'customer_id' => $customerId,
                'session_token' => $sessionToken,
                'currency' => config('app.currency', 'USD'),
            ]);
        }

        return $cart;
    }

    /**
     * Merge guest cart into user cart.
     * * Transfers items from a guest session cart into an authenticated customer's cart.
     */
    public function mergeGuestCartToCustomer(int $customerId, string $sessionToken): void
    {
        $guestCart = Cart::query()->where('session_token', $sessionToken)->first();
        $customerCart = Cart::query()->where('customer_id', $customerId)->first();

        if (! $guestCart) {
            return;
        }

        if (! $customerCart) {
            $guestCart->update([
                'customer_id' => $customerId,
                'session_token' => null,
            ]);

            return;
        }

        foreach ($guestCart->items as $item) {
            $existing = $customerCart->items()
                ->where('product_id', $item->product_id)
                ->first();

            if ($existing) {
                $existing->increment('qty', $item->qty);
            } else {
                $customerCart->items()->create([
                    'product_id' => $item->product_id,
                    'qty' => $item->qty,
                    'unit_price' => $item->unit_price,
                    'original_price' => $item->original_price,
                ]);
            }
        }

        $guestCart->delete();
    }

    /**
     * Add an item to the cart.
     * * Validates stock and product availability before adding or incrementing quantity.
     *
     * @param  array  $data  Validated item data.
     *
     * @throws Throwable
     */
    public function addItemToCart(Cart $cart, array $data): CartItem
    {
        $product = Product::query()->findOrFail($data['product_id']);

        if (! $product->is_active) {
            abort(422, 'Product not available.');
        }

        if ($product->stock !== null && $product->stock < $data['quantity']) {
            abort(422, 'Insufficient stock.');
        }

        $item = DB::transaction(function () use ($cart, $product, $data) {
            $item = $cart->items()->where('product_id', $product->id)->first();

            if ($item) {
                $newQty = $item->qty + $data['quantity'];

                if ($product->stock !== null && $product->stock < $newQty) {
                    abort(422, 'Insufficient stock.');
                }

                $item->update(['qty' => $newQty]);

                return $item;
            }

            return $cart->items()->create([
                'product_id' => $product->id,
                'qty' => $data['quantity'],
                'unit_price' => $product->price_cents,
                'original_price' => $product->price_cents,
                'discount_amount' => 0,
            ]);
        });

        CartItemAdded::dispatch($item);

        return $item;
    }

    /**
     * Update the quantity of a cart item.
     * * Adjusts quantity or removes the item completely if the new quantity is zero or less.
     *
     * @param  array  $data  Validated update data.
     */
    public function updateCartItem(CartItem $item, array $data): ?CartItem
    {
        $product = $item->product;

        if ($data['quantity'] <= 0) {
            $item->delete();

            return null;
        }

        if ($product->stock !== null && $product->stock < $data['quantity']) {
            abort(422, 'Insufficient stock.');
        }

        $item->update(['qty' => $data['quantity']]);

        return $item->fresh();
    }

    /**
     * Remove an item from the cart completely.
     * * Deletes the specified cart item.
     */
    public function removeCartItem(CartItem $item): void
    {
        $item->delete();
    }

    /**
     * Apply a coupon to the cart.
     * * Validates the coupon rules (expiration, usage limits, minimum amount) and calculates updated totals.
     *
     * @param  array  $data  Validated coupon data.
     */
    public function applyCouponToCart(Cart $cart, array $data): array
    {
        $coupon = Coupon::query()->where('code', $data['code'])->firstOrFail();
        $subtotal = $cart->subtotal();

        if (! $coupon->isRedeemable($subtotal)) {
            abort(422, 'Coupon not redeemable.');
        }

        $cart->update(['coupon_id' => $coupon->id]);

        return $this->getCartTotals($cart->fresh(['items', 'coupon']));
    }

    /**
     * Validate the cart for checkout.
     * Ensures the cart is not empty and all items are active and in stock.
     */
    public function validateCartForCheckout(Cart $cart): void
    {
        if ($cart->items->isEmpty()) {
            abort(422, 'Cart is empty.');
        }

        foreach ($cart->items as $item) {
            $product = $item->product;

            if (! $product || ! $product->is_active) {
                abort(422, 'Invalid product in cart.');
            }

            if ($product->stock !== null && $product->stock < $item->qty) {
                abort(422, 'Stock no longer available.');
            }
        }
    }

    /**
     * Calculate cart totals.
     * * Computes the subtotal, discounts, tax, and final total.
     */
    public function getCartTotals(Cart $cart): array
    {
        $cart->loadMissing(['items', 'coupon']);

        $subtotal = (int) $cart->subtotal();

        $discount = $cart->coupon
            ? (int) $cart->coupon->discountFor($subtotal)
            : 0;

        $tax = (int) round(($subtotal - $discount) * 0.075);

        $total = max(0, $subtotal - $discount + $tax);

        return compact('subtotal', 'discount', 'tax', 'total');
    }

    /**
     * Delete expired carts.
     * Cleans up abandoned carts older than a specified threshold (e.g., 7 days).
     *
     * @return int The number of deleted carts.
     */
    public function deleteExpiredCarts(): int
    {
        return Cart::query()->where('updated_at', '<', now()->subDays(7))->delete();
    }
}
