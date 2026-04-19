<?php

namespace App\Services\Tenant;

use App\Models\Tenant\Product;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

/**
 * Class ProductService
 * * Handles business logic related to tenant products.
 */
class ProductService
{
    /**
     * Retrieve a paginated, filtered list of active products.
     *
     * @param string|null $categorySlug
     * @param string|null $searchQuery
     * @return LengthAwarePaginator
     */
    public function getPaginatedProducts(?string $categorySlug, ?string $searchQuery): LengthAwarePaginator
    {
        return Product::query()
            ->where('is_active', true)
            ->when($categorySlug, fn($q, $c) => $q->whereHas('category', fn($q) => $q->where('slug', $c)))
            ->when($searchQuery, fn($q, $s) => $q->where('name', 'like', "%{$s}%"))
            ->latest()
            ->paginate(24);
    }

    /**
     * Create a new product.
     *
     * @param array $data Validated product data.
     * @return Product
     */
    public function createProduct(array $data): Product
    {
        $data['currency'] = $data['currency'] ?? 'USD';

        return Product::query()->create($data);
    }

    /**
     * Retrieve a specific active product by its slug.
     *
     * @param string $slug
     * @return Product
     */
    public function getProductBySlug(string $slug): Product
    {
        return Product::query()->where('slug', $slug)->where('is_active', true)->firstOrFail();
    }

    /**
     * Update an existing product.
     *
     * @param Product $product
     * @param array $data Validated update data.
     * @return Product
     */
    public function updateProduct(Product $product, array $data): Product
    {
        $product->update($data);

        return $product->fresh();
    }

    /**
     * Delete a product.
     *
     * @param Product $product
     * @return void
     */
    public function deleteProduct(Product $product): void
    {
        $product->delete();
    }
}
