<?php

namespace App\Http\Controllers\Tenant\Api;

use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\Tenant\Cart\AddItemRequest;
use App\Http\Requests\Tenant\Cart\ApplyCouponRequest;
use App\Http\Requests\Tenant\Cart\UpdateItemRequest;
use App\Models\Tenant\Cart;
use App\Services\Tenant\CartService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Throwable;

/**
 * Cart Endpoints
 * * Handles the creation, retrieval, updating, and checkout preparation of customer carts.
 */
class CartController extends Controller
{
    /**
     * Create a new CartController instance.
     */
    public function __construct(
        private readonly CartService $cartService
    ) {}

    /**
     * Resolve the active cart using headers or auth.
     */
    private function resolveCart(Request $request): Cart
    {
        $customerId = $request->user()?->customer?->id;
        $sessionToken = $request->header('X-Cart-Id') ?: Str::uuid()->toString();

        $request->headers->set('X-Cart-Id', $sessionToken);

        $cart = $this->cartService->resolveCart($customerId, $sessionToken);

        if ($customerId) {
            $this->cartService->mergeGuestCartToCustomer($customerId, $sessionToken);
        }

        return $cart;
    }

    /**
     * Get cart details and totals.
     */
    public function show(Request $request): JsonResponse
    {
        $cart = $this->resolveCart($request);
        $totals = $this->cartService->getCartTotals($cart);

        return ApiResponse::success(
            [
                'cart' => $cart->load(['items.product', 'coupon']),
                'totals' => $totals,
            ],
            'Cart retrieved successfully'
        )->header('X-Cart-Id', $request->header('X-Cart-Id'));
    }

    /**
     * Add a new item to the cart.
     *
     * @throws Throwable
     */
    public function addItem(AddItemRequest $request): JsonResponse
    {
        $cart = $this->resolveCart($request);
        $item = $this->cartService->addItemToCart($cart, $request->validated());

        return ApiResponse::success(
            ['item' => $item],
            'Item added to cart successfully'
        )->header('X-Cart-Id', $request->header('X-Cart-Id'));
    }

    /**
     * Update the quantity of a specific cart item.
     */
    public function updateItem(UpdateItemRequest $request, int $itemId): JsonResponse
    {
        $cart = $this->resolveCart($request);
        $item = $cart->items()->findOrFail($itemId);

        $updatedItem = $this->cartService->updateCartItem($item, $request->validated());

        return ApiResponse::success(
            ['item' => $updatedItem],
            'Cart item updated successfully'
        )->header('X-Cart-Id', $request->header('X-Cart-Id'));
    }

    /**
     * Remove an item from the cart.
     */
    public function removeItem(Request $request, int $itemId): JsonResponse
    {
        $cart = $this->resolveCart($request);
        $item = $cart->items()->findOrFail($itemId);

        $this->cartService->removeCartItem($item);

        return ApiResponse::success(
            null,
            'Item removed from cart successfully'
        )->header('X-Cart-Id', $request->header('X-Cart-Id'));
    }

    /**
     * Apply a discount coupon to the cart.
     */
    public function applyCoupon(ApplyCouponRequest $request): JsonResponse
    {
        $cart = $this->resolveCart($request);
        $totals = $this->cartService->applyCouponToCart($cart, $request->validated());

        return ApiResponse::success(
            ['totals' => $totals],
            'Coupon applied successfully'
        )->header('X-Cart-Id', $request->header('X-Cart-Id'));
    }
}
