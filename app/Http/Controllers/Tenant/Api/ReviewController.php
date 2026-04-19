<?php

namespace App\Http\Controllers\Tenant\Api;

use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\Tenant\Review\StoreReviewRequest;
use App\Models\Tenant\Review;
use App\Services\Tenant\ReviewService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Review Endpoints
 * * Handles the creation, retrieval, and approval of product reviews.
 */
class ReviewController extends Controller
{
    /**
     * Create a new ReviewController instance.
     *
     * @param ReviewService $reviewService
     */
    public function __construct(
        private readonly ReviewService $reviewService
    ) {}

    /**
     * List approved product reviews.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $reviews = $this->reviewService->getPaginatedProductReviews(
            $request->query('product_id')
        );

        return ApiResponse::success(
            ['reviews' => $reviews],
            'Reviews retrieved successfully'
        );
    }

    /**
     * Submit a new product review.
     *
     * @param StoreReviewRequest $request
     * @return JsonResponse
     */
    public function store(StoreReviewRequest $request): JsonResponse
    {
        $review = $this->reviewService->createReview(
            $request->user()->id,
            $request->validated()
        );

        return ApiResponse::success(
            ['review' => $review],
            'Review submitted successfully',
            null,
            201
        );
    }

    /**
     * Approve a product review.
     *
     * @param Review $review
     * @return JsonResponse
     */
    public function approve(Review $review): JsonResponse
    {
        $approvedReview = $this->reviewService->approveReview($review);

        return ApiResponse::success(
            ['review' => $approvedReview],
            'Review approved successfully'
        );
    }
}
