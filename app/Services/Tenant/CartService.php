<?php

namespace App\Services\Tenant;

use App\Models\Tenant\Cart;
use App\Models\Tenant\CartItem;
use App\Models\Tenant\Product;
use App\Models\Tenant\Coupon;
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
     *
     * @param int|null $customerId
     * @param string|null $sessionToken
     * @return Cart
     */
    public function resolveCart(?int $customerId, ?string $sessionToken): Cart
    {
        $cart = Cart::query()
            ->when($customerId, fn($q) => $q->where('customer_id', $customerId))
            ->when(!$customerId && $sessionToken, fn($q) => $q->where('session_token', $sessionToken))
            ->first();

        if (!$cart) {
            $cart = Cart::query()->create([
                'customer_id'   => $customerId,
                'session_token' => $sessionToken,
                'currency'      => config('app.currency', 'USD'),
            ]);
        }

        return $cart;
    }

    /**
     * Add a product to the cart.
     *
     * @param Cart $cart
     * @param array $data Validated item data.
     * @return CartItem
     * @throws Throwable
     */
    public function addItemToCart(Cart $cart, array $data): CartItem
    {
        $product = Product::query()->findOrFail($data['product_id']);

        if (!$product->is_active) {
            abort(422, 'Product not available.');
        }

        if ($product->stock !== null && $product->stock < $data['quantity']) {
            abort(422, 'Insufficient stock.');
        }

        return DB::transaction(function () use ($cart, $product, $data) {
            $item = $cart->items()->where('product_id', $product->id)->first();

            if ($item) {
                $item->qty += $data['quantity'];
                $item->save();

                return $item;
            }

            return $cart->items()->create([
                'product_id' => $product->id,
                'qty'        => $data['quantity'],
                'unit_price' => $product->price_cents,
            ]);
        });
    }

    /**
     * Update the quantity of a cart item.
     *
     * @param CartItem $item
     * @param array $data Validated update data.
     * @return CartItem|null
     */
    public function updateCartItem(CartItem $item, array $data): ?CartItem
    {
        if ($data['quantity'] <= 0) {
            $item->delete();
            return null;
        }

        $item->update(['qty' => $data['quantity']]);

        return $item->fresh();
    }

    /**
     * Remove an item from the cart completely.
     *
     * @param CartItem $item
     * @return void
     */
    public function removeCartItem(CartItem $item): void
    {
        $item->delete();
    }

    /**
     * Apply a coupon to the cart and return the updated totals.
     *
     * @param Cart $cart
     * @param array $data Validated coupon data.
     * @return array
     */
    public function applyCouponToCart(Cart $cart, array $data): array
    {
        $coupon = Coupon::query()->where('code', $data['code'])->firstOrFail();
        $subtotal = $cart->subtotal();

        if (!$coupon->isRedeemable($subtotal)) {
            abort(422, 'Coupon not redeemable.');
        }

        $cart->update(['coupon_id' => $coupon->id]);

        return $this->getCartTotals($cart->fresh(['items', 'coupon']));
    }

    /**
     * Calculate cart totals including subtotal, discount, tax, and final total.
     *
     * @param Cart $cart
     * @return array
     */
    public function getCartTotals(Cart $cart): array
    {
        $cart->loadMissing(['items', 'coupon']);

        $subtotal = $cart->subtotal();
        $discount = $cart->coupon ? $cart->coupon->discountFor($subtotal) : 0.0;
        $tax      = round(($subtotal - $discount) * 0.0, 2); // Hook for TaxService
        $total    = max(0, $subtotal - $discount + $tax);

        return compact('subtotal', 'discount', 'tax', 'total');
    }
}
