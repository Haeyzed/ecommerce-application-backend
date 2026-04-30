<?php

namespace App\Services\Tenant;

use App\Models\Tenant\Cart;
use App\Models\Tenant\Order;
use App\Models\Tenant\OrderItem;
use App\Models\Tenant\Product;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Throwable;

/**
 * Class OrderService
 * * Handles business logic related to tenant orders.
 */
class OrderService
{
    /**
     * Create a new OrderService instance.
     */
    public function __construct(
        private readonly CartService $cartService,
        private readonly CouponService $couponService,
    ) {}

    /**
     * Retrieve a paginated list of orders for a specific customer.
     */
    public function getPaginatedCustomerOrders(int $customerId): LengthAwarePaginator
    {
        return Order::query()
            ->with('items')
            ->where('customer_id', $customerId)
            ->latest()
            ->paginate(20);
    }

    /**
     * Retrieve a specific order by its ID and the customer's ID.
     */
    public function getCustomerOrderById(int $customerId, int $orderId): Order
    {
        return Order::query()
            ->with('items')
            ->where('customer_id', $customerId)
            ->findOrFail($orderId);
    }

    /**
     * Create a new order directly from an array of items (direct checkout).
     *
     * @param  array  $data  Validated order data.
     *
     * @throws Throwable
     */
    public function createOrder(int $customerId, array $data): Order
    {
        return DB::transaction(function () use ($customerId, $data) {
            $total = 0;
            $rows = [];

            foreach ($data['items'] as $line) {
                $p = Product::query()->lockForUpdate()->findOrFail($line['product_id']);

                if ($p->stock !== null && $p->stock < $line['quantity']) {
                    abort(422, "Out of stock: {$p->name}");
                }

                $p->decrement('stock', $line['quantity']);
                $total += $p->price_cents * $line['quantity'];

                $rows[] = [
                    'product_id' => $p->id,
                    'name' => $p->name,
                    'unit_price_cents' => $p->price_cents,
                    'quantity' => $line['quantity'],
                ];
            }

            $order = Order::query()->create([
                'number' => 'ORD-'.strtoupper(Str::random(10)),
                'customer_id' => $customerId,
                'status' => 'pending',
                'total_cents' => $total,
                'currency' => 'USD',
                'shipping_address' => $data['shipping_address'],
            ]);

            $order->items()->createMany($rows);

            return $order->load('items');
        });
    }

    /**
     * Convert an active cart into a paid-pending order.
     *
     * @throws Throwable
     */
    public function checkoutCart(Cart $cart, array $payload): Order
    {
        if ($cart->items()->count() === 0) {
            abort(422, 'Cart is empty.');
        }

        return DB::transaction(function () use ($cart, $payload) {
            $totals = $this->cartService->getCartTotals($cart);

            $order = Order::query()->create([
                'number' => 'ORD-'.strtoupper(Str::random(10)),
                'customer_id' => $cart->customer_id,
                'status' => 'pending',
                'subtotal' => $totals['subtotal'],
                'discount' => $totals['discount'],
                'tax' => $totals['tax'],
                'total' => $totals['total'],
                'currency' => $cart->currency,
                'shipping_address' => $payload['shipping_address'] ?? null,
                'billing_address' => $payload['billing_address'] ?? $payload['shipping_address'] ?? null,
                'notes' => $payload['notes'] ?? null,
            ]);

            foreach ($cart->items as $item) {
                OrderItem::query()->create([
                    'order_id' => $order->id,
                    'product_id' => $item->product_id,
                    'qty' => $item->qty,
                    'unit_price' => $item->unit_price,
                    'line_total' => $item->qty * $item->unit_price,
                ]);

                // Decrement stock
                $item->product()->decrement('stock', $item->qty);
            }

            if ($cart->coupon) {
                $this->couponService->redeemCoupon($cart->coupon);
            }

            // Wipe cart
            $cart->items()->delete();
            $cart->delete();

            return $order->load('items');
        });
    }

    /**
     * Mark an order as paid.
     */
    public function markOrderPaid(Order $order): Order
    {
        $order->update([
            'status' => 'paid',
            'paid_at' => now(),
        ]);

        return $order->fresh();
    }

    /**
     * Cancel an order and restore stock levels.
     *
     * @throws Throwable
     */
    public function cancelOrder(Order $order, ?string $reason = null): Order
    {
        return DB::transaction(function () use ($order, $reason) {
            if (in_array($order->status, ['shipped', 'completed'])) {
                abort(422, 'Cannot cancel a fulfilled order.');
            }

            // Restock products
            foreach ($order->items as $item) {
                $item->product()->increment('stock', $item->qty);
            }

            $order->update([
                'status' => 'cancelled',
                'cancellation_reason' => $reason,
                'cancelled_at' => now(),
            ]);

            return $order->fresh();
        });
    }
}
