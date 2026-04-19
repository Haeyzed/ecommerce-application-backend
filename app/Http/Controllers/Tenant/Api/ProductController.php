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
     *
     * @param ProductService $productService
     */
    public function __construct(
        private readonly ProductService $productService
    ) {}

    /**
     * List all active products.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $products = $this->productService->getPaginatedProducts(
            $request->query('category'),
            $request->query('q')
        );

        return ApiResponse::success(
            ['products' => $products],
            'Products retrieved successfully'
        );
    }

    /**
     * Create a new product.
     *
     * @param StoreProductRequest $request
     * @return JsonResponse
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
     * Get product details.
     *
     * @param string $slug
     * @return JsonResponse
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
     * Update a product.
     *
     * @param UpdateProductRequest $request
     * @param Product $product
     * @return JsonResponse
     */
    public function update(UpdateProductRequest $request, Product $product): JsonResponse
    {
        $updatedProduct = $this->productService->updateProduct($product, $request->validated());

        return ApiResponse::success(
            ['product' => $updatedProduct],
            'Product updated successfully'
        );
    }

    /**
     * Delete a product.
     *
     * @param Product $product
     * @return JsonResponse
     */
    public function destroy(Product $product): JsonResponse
    {
        $this->productService->deleteProduct($product);

        return ApiResponse::success(null, 'Product deleted successfully');
    }
}
