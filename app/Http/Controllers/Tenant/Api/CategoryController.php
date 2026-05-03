<?php

namespace App\Http\Controllers\Tenant\Api;

use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\Tenant\Category\StoreCategoryRequest;
use App\Services\Tenant\CategoryService;
use Illuminate\Http\JsonResponse;

/**
 * Category Endpoints
 * * Handles the retrieval and creation of product categories.
 */
class CategoryController extends Controller
{
    /**
     * Create a new CategoryController instance.
     */
    public function __construct(
        private readonly CategoryService $categoryService
    ) {
        $this->middleware('permission:view categories')->only(['index']);
        $this->middleware('permission:create categories')->only(['store']);
    }

    /**
     * List all categories.
     */
    public function index(): JsonResponse
    {
        $categories = $this->categoryService->getCategoryTree();

        return ApiResponse::success(
            ['categories' => $categories],
            'Categories retrieved successfully'
        );
    }

    /**
     * Create a new category.
     */
    public function store(StoreCategoryRequest $request): JsonResponse
    {
        $category = $this->categoryService->createCategory($request->validated());

        return ApiResponse::success(
            ['category' => $category],
            'Category created successfully',
            null,
            201
        );
    }
}
