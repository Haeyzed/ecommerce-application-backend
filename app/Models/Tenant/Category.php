<?php

namespace App\Models\Tenant;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

/**
 * Class Category
 *
 * Represents a product category within a tenant's store.
 *
 * @property int $id The unique identifier of the category.
 * @property string $name The display name of the category.
 * @property string $slug The URL-friendly version of the category name.
 * @property Carbon|null $created_at Timestamp of when the category was created.
 * @property Carbon|null $updated_at Timestamp of when the category was last updated.
 *
 * @property-read Collection|Product[] $products The products belonging to this category.
 *
 * @package App\Models\Tenant
 */
class Category extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'slug',
    ];

    /**
     * Get the products associated with the category.
     *
     * @return HasMany
     */
    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }
}
