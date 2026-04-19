<?php

namespace App\Models\Tenant;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

/**
 * Class Product
 *
 * Represents a purchasable item within a tenant's store.
 *
 * @property int $id The unique identifier of the product.
 * @property string $name The display name of the product.
 * @property string $slug The URL-friendly version of the product name.
 * @property string|null $description The detailed description of the product.
 * @property int $price_cents The price of the product in cents.
 * @property string $currency The ISO currency code for the product.
 * @property int $stock The current available stock quantity.
 * @property int|null $category_id The foreign key referencing the product's category.
 * @property string|null $image_url The URL pointing to the product's main image.
 * @property bool $is_active Indicates whether the product is available for sale.
 * @property Carbon|null $created_at Timestamp of when the product was created.
 * @property Carbon|null $updated_at Timestamp of when the product was last updated.
 *
 * @property-read Category|null $category The category this product belongs to.
 *
 * @package App\Models\Tenant
 */
class Product extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'slug',
        'description',
        'price_cents',
        'currency',
        'stock',
        'category_id',
        'image_url',
        'is_active',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'is_active' => 'bool',
            'price_cents' => 'int',
            'stock' => 'int',
        ];
    }

    /**
     * The "booted" method of the model.
     * * Handles automatic slug generation upon product creation.
     *
     * @return void
     */
    protected static function booted(): void
    {
        static::creating(function (Product $p) {
            if (empty($p->slug)) {
                $p->slug = Str::slug($p->name) . '-' . Str::lower(Str::random(5));
            }
        });
    }

    /**
     * Get the category that the product belongs to.
     *
     * @return BelongsTo
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }
}
