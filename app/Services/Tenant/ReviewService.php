<?php

namespace App\Services\Tenant;

use App\Models\Tenant\Review;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

/**
 * Class ReviewService
 * * Handles business logic related to tenant product reviews.
 */
class ReviewService
{
    /**
     * Retrieve a paginated list of approved reviews for a specific product.
     *
     * @param int|null $productId
     * @return LengthAwarePaginator
     */
    public function getPaginatedProductReviews(?int $productId): LengthAwarePaginator
    {
        return Review::query()
            ->when($productId, fn($q) => $q->where('product_id', $productId))
            ->where('is_approved', true)
            ->latest()
            ->paginate(20);
    }

    /**
     * Create a new product review.
     *
     * @param int $customerId The ID of the customer writing the review.
     * @param array $data Validated review data.
     * @return Review
     */
    public function createReview(int $customerId, array $data): Review
    {
        $data['customer_id'] = $customerId;
        $data['is_approved'] = $data['is_approved'] ?? false;

        return Review::query()->create($data);
    }

    /**
     * Approve a pending review.
     *
     * @param Review $review
     * @return Review
     */
    public function approveReview(Review $review): Review
    {
        $review->update(['is_approved' => true]);

        return $review->fresh();
    }
}
