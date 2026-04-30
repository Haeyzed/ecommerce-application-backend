<?php

namespace App\Http\Controllers\Tenant\Api;

use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\Tenant\Product\StoreProductRequest;
use App\Http\Requests\Tenant\Product\UpdateProductRequest;
use App\Models\Tenant\Product;
use App\Services\Tenant\ProductService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Product Endpoints
 * * Handles the creation, retrieval, updating, and deletion of storefront products.
 */
class ProductController extends Controller
{
    /**
     * Create a new ProductController instance.
     */
    public function __construct(
        private readonly ProductService $productService
    ) {}

    /**
     * List all active products.
     */
    public function index(Request $request): JsonResponse
    {
        $filters = [
            'is_active' => true,
            'category_slug' => $request->query('category'),
            'search' => $request->query('q'),
        ];

        $products = $this->productService->getPaginatedProducts($filters);

        return ApiResponse::success(
            ['products' => $products],
            'Products retrieved successfully'
        );
    }

    /**
     * Get product details.
     */
    public function show(string $slug): JsonResponse
    {
        $product = $this->productService->getProductBySlug($slug);

        return ApiResponse::success(
            ['product' => $product],
            'Product retrieved successfully'
        );
    }

    /**
     * Create a new product.
     */
    public function store(StoreProductRequest $request): JsonResponse
    {
        $product = $this->productService->createProduct($request->validated());

        return ApiResponse::success(
            ['product' => $product],
            'Product created successfully',
            null,
            201
        );
    }

    /**
     * Update a product.
     */
    public function update(UpdateProductRequest $request, int $id): JsonResponse
    {
        $product = Product::query()->findOrFail($id);
        $updatedProduct = $this->productService->updateProduct($product, $request->validated());

        return ApiResponse::success(
            ['product' => $updatedProduct],
            'Product updated successfully'
        );
    }

    /**
     * Delete a product.
     */
    public function destroy(int $id): JsonResponse
    {
        $product = Product::query()->findOrFail($id);
        $this->productService->deleteProduct($product);

        return ApiResponse::success(null, 'Product deleted successfully');
    }
}
