<?php

namespace App\Services\Tenant;

use App\Models\Tenant\InventoryMovement;
use App\Models\Tenant\Product;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Throwable;

/**
 * Class ProductService
 * * Handles business logic related to tenant products.
 */
class ProductService
{
    /**
     * Retrieve a paginated, filtered list of products.
     *
     * @param  array  $filters  Query filters (e.g., category_slug, search, is_active)
     * @param  int  $perPage  Items per page
     */
    public function getPaginatedProducts(array $filters = [], int $perPage = 24): LengthAwarePaginator
    {
        return Product::query()
            ->when(isset($filters['is_active']), fn ($q) => $q->where('is_active', $filters['is_active']))
            ->when($filters['category_id'] ?? null, fn ($q, $v) => $q->where('category_id', $v))
            ->when($filters['category_slug'] ?? null, fn ($q, $c) => $q->whereHas('category', fn ($q) => $q->where('slug', $c)))
            ->when($filters['search'] ?? null, fn ($q, $s) => $q->where('name', 'like', "%{$s}%"))
            ->latest()
            ->paginate($perPage);
    }

    /**
     * Retrieve a specific active product by its slug.
     */
    public function getProductBySlug(string $slug): Product
    {
        return Product::query()
            ->where('slug', $slug)
            ->where('is_active', true)
            ->firstOrFail();
    }

    /**
     * Create a new product.
     *
     * @param  array  $data  Validated product data.
     */
    public function createProduct(array $data): Product
    {
        $data['currency'] = $data['currency'] ?? 'USD';

        return Product::query()->create($data);
    }

    /**
     * Update an existing product.
     *
     * @param  array  $data  Validated update data.
     */
    public function updateProduct(Product $product, array $data): Product
    {
        $product->update($data);

        return $product->fresh();
    }

    /**
     * Delete a product.
     */
    public function deleteProduct(Product $product): void
    {
        $product->delete();
    }

    /**
     * Safely adjust the stock of a product and record the movement.
     *
     * @param  int  $delta  The positive/negative quantity change.
     * @param  string  $reason  The reason for adjustment.
     * @param  string|null  $note  Optional context note.
     *
     * @throws Throwable
     */
    public function adjustStock(Product $product, int $delta, string $reason, ?string $note = null): Product
    {
        return DB::transaction(function () use ($product, $delta, $reason, $note) {
            $product->lockForUpdate()->refresh();
            $product->stock = max(0, ($product->stock) + $delta);
            $product->save();

            InventoryMovement::query()->create([
                'product_id' => $product->id,
                'qty_change' => $delta,
                'reason' => $reason,
                'note' => $note,
            ]);

            return $product->fresh();
        });
    }
}
