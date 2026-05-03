<?php

namespace App\Http\Controllers\Tenant\Api;

use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\Tenant\Coupon\StoreCouponRequest;
use App\Http\Requests\Tenant\Coupon\UpdateCouponRequest;
use App\Models\Tenant\Coupon;
use App\Services\Tenant\CouponService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Coupon Endpoints
 * * Handles the creation, retrieval, updating, and deletion of storefront coupons.
 */
class CouponController extends Controller
{
    /**
     * Create a new CouponController instance.
     */
    public function __construct(
        private readonly CouponService $couponService
    ) {
        $this->middleware('permission:view coupons')->only(['index', 'show']);
        $this->middleware('permission:create coupons')->only(['store']);
        $this->middleware('permission:update coupons')->only(['update']);
        $this->middleware('permission:delete coupons')->only(['destroy']);
    }

    /**
     * List all coupons.
     */
    public function index(Request $request): JsonResponse
    {
        $coupons = $this->couponService->getPaginatedCoupons();

        return ApiResponse::success(
            ['coupons' => $coupons],
            'Coupons retrieved successfully'
        );
    }

    /**
     * Create a new coupon.
     */
    public function store(StoreCouponRequest $request): JsonResponse
    {
        $coupon = $this->couponService->createCoupon($request->validated());

        return ApiResponse::success(
            ['coupon' => $coupon],
            'Coupon created successfully',
            null,
            201
        );
    }

    /**
     * Get coupon details.
     */
    public function show(int $id): JsonResponse
    {
        $coupon = $this->couponService->getCouponById($id);

        return ApiResponse::success(
            ['coupon' => $coupon],
            'Coupon retrieved successfully'
        );
    }

    /**
     * Update a coupon.
     */
    public function update(UpdateCouponRequest $request, Coupon $coupon): JsonResponse
    {
        $updatedCoupon = $this->couponService->updateCoupon($coupon, $request->validated());

        return ApiResponse::success(
            ['coupon' => $updatedCoupon],
            'Coupon updated successfully'
        );
    }

    /**
     * Delete a coupon.
     */
    public function destroy(Coupon $coupon): JsonResponse
    {
        $this->couponService->deleteCoupon($coupon);

        return ApiResponse::success(null, 'Coupon deleted successfully');
    }
}
