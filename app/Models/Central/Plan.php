<?php

namespace App\Models\Central;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;

/**
 * Class Plan
 *
 * Represents a SaaS subscription plan available to tenants.
 *
 * @property int $id The unique identifier of the plan.
 * @property string $name The display name of the plan.
 * @property string $slug The URL-friendly slug of the plan name.
 * @property string|null $description The description of the plan.
 * @property int $price_cents The cost of the plan in minor units.
 * @property string $currency The ISO currency code.
 * @property string|null $interval The billing interval (e.g., month, year).
 * @property array|null $features JSON array of plan features.
 * @property array|null $limits JSON array of resource limits.
 * @property-read int $max_products Derived from limits.max_products.
 * @property-read bool $allows_custom_domain Derived from limits.allows_custom_domain.
 * @property bool $is_active Indicates if the plan is currently available for new subscriptions.
 * @property Carbon|null $created_at Timestamp of when the plan was created.
 * @property Carbon|null $updated_at Timestamp of when the plan was last updated.
 * @property-read Collection|Subscription[] $subscriptions The subscriptions associated with this plan.
 */
class Plan extends Model implements AuditableContract
{
    use Auditable, HasSlug;

    /**
     * @var string
     */
    protected $connection = 'central';

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
        'interval',
        'features',
        'limits',
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
            'features' => 'array',
            'limits' => 'array',
            'price_cents' => 'integer',
            'is_active' => 'boolean',
        ];
    }

    /**
     * @return Attribute<int, never>
     */
    protected function maxProducts(): Attribute
    {
        return Attribute::get(fn (): int => (int) data_get($this->limits, 'max_products', 0));
    }

    /**
     * @return Attribute<bool, never>
     */
    protected function allowsCustomDomain(): Attribute
    {
        return Attribute::get(fn (): bool => (bool) data_get($this->limits, 'allows_custom_domain', false));
    }

    /**
     * Get the options for generating the slug.
     */
    public function getSlugOptions(): SlugOptions
    {
        return SlugOptions::create()
            ->generateSlugsFrom('name')
            ->saveSlugsTo('slug');
    }

    /**
     * Get the subscriptions associated with this plan.
     */
    public function subscriptions(): HasMany
    {
        return $this->hasMany(Subscription::class);
    }
}
