<?php

namespace App\Services\Tenant;

use App\Models\Tenant\Category;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Str;

/**
 * Class CategoryService
 * * Handles business logic related to tenant product categories.
 */
class CategoryService
{
    /**
     * Retrieve the category tree.
     *
     * @return Collection
     */
    public function getCategoryTree(): Collection
    {
        return Category::query()
            ->orderBy('name')
            ->get();
    }

    /**
     * Create a new category.
     *
     * @param array $data Validated category data.
     * @return Category
     */
    public function createCategory(array $data): Category
    {
        $data['slug'] = $data['slug'] ?? Str::slug($data['name']);

        return Category::query()->create($data);
    }
}
