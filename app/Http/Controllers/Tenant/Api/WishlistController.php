<?php

namespace App\Http\Controllers\Tenant\Api;

use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\Tenant\Wishlist\ToggleWishlistRequest;
use App\Services\Tenant\WishlistService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Wishlist Endpoints
 * * Handles retrieving and toggling customer wishlist items.
 */
class WishlistController extends Controller
{
    /**
     * Create a new WishlistController instance.
     */
    public function __construct(
        private readonly WishlistService $wishlistService
    ) {}

    /**
     * List customer wishlist items.
     */
    public function index(Request $request): JsonResponse
    {
        $wishlist = $this->wishlistService->getCustomerWishlist($request->user()->id);

        return ApiResponse::success(
            ['wishlist' => $wishlist],
            'Wishlist retrieved successfully'
        );
    }

    /**
     * Toggle a product in the wishlist.
     */
    public function toggle(ToggleWishlistRequest $request): JsonResponse
    {
        $result = $this->wishlistService->toggleWishlistItem(
            $request->user()->id,
            $request->validated()
        );

        return ApiResponse::success(
            $result,
            'Wishlist updated successfully'
        );
    }
}
