<?php

namespace App\Models\Central;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * Class Plan
 *
 * Represents a subscription plan available for tenants.
 *
 * @property int $id The unique identifier for the plan.
 * @property string $name The display name of the subscription plan (e.g., 'Pro', 'Basic').
 * @property string $slug The URL-friendly version of the plan name.
 * @property int $price_cents The price of the plan in cents.
 * @property int $max_products The maximum number of products a tenant can create on this plan.
 * @property bool $allows_custom_domain Indicates whether the plan allows mapping a custom domain.
 * @property Carbon|null $created_at Timestamp of when the plan was created.
 * @property Carbon|null $updated_at Timestamp of when the plan was last updated.
 *
 * @package App\Models\Central
 */
class Plan extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'slug',
        'price_cents',
        'max_products',
        'allows_custom_domain'
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'allows_custom_domain' => 'bool',
            'price_cents' => 'int',
            'max_products' => 'int',
        ];
    }
}
