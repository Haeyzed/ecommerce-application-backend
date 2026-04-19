<?php

namespace App\Services\Tenant;

use App\Models\Tenant\Coupon;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Str;

/**
 * Class CouponService
 * * Handles business logic related to tenant coupons.
 */
class CouponService
{
    /**
     * Retrieve a paginated list of coupons.
     *
     * @return LengthAwarePaginator
     */
    public function getPaginatedCoupons(): LengthAwarePaginator
    {
        return Coupon::query()
            ->orderByDesc('id')
            ->paginate(20);
    }

    /**
     * Create a new coupon.
     *
     * @param array $data Validated coupon data.
     * @return Coupon
     */
    public function createCoupon(array $data): Coupon
    {
        $data['code'] = strtoupper($data['code'] ?? Str::random(8));
        $data['type'] = $data['type'] ?? 'percent';

        return Coupon::query()->create($data);
    }

    /**
     * Retrieve a specific coupon by its ID.
     *
     * @param int $id
     * @return Coupon
     */
    public function getCouponById(int $id): Coupon
    {
        return Coupon::query()->findOrFail($id);
    }

    /**
     * Update an existing coupon.
     *
     * @param Coupon $coupon
     * @param array $data Validated update data.
     * @return Coupon
     */
    public function updateCoupon(Coupon $coupon, array $data): Coupon
    {
        $coupon->update($data);

        return $coupon->fresh();
    }

    /**
     * Delete a coupon.
     *
     * @param Coupon $coupon
     * @return void
     */
    public function deleteCoupon(Coupon $coupon): void
    {
        $coupon->delete();
    }

    /**
     * Redeem a coupon.
     * * Increments the used count for the coupon.
     *
     * @param Coupon $coupon
     * @return Coupon
     */
    public function redeemCoupon(Coupon $coupon): Coupon
    {
        $coupon->increment('used_count');

        return $coupon->fresh();
    }
}
