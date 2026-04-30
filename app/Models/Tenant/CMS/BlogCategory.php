<?php

namespace App\Models\Tenant\CMS;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;

/**
 * Class BlogCategory
 *
 * Represents a classification for blog posts.
 *
 * @property int $id The unique identifier of the category.
 * @property string $name The display name of the category.
 * @property string $slug The URL-friendly version of the category name.
 * @property string|null $description A brief description of the category.
 * @property Carbon|null $created_at Timestamp of when the category was created.
 * @property Carbon|null $updated_at Timestamp of when the category was last updated.
 *
 * @method static Builder filter(array $filters)
 */
class BlogCategory extends Model implements AuditableContract
{
    use Auditable, HasSlug;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'slug',
        'description',
    ];

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
     * Get the posts belonging to this category.
     */
    public function posts(): HasMany
    {
        return $this->hasMany(BlogPost::class);
    }

    /**
     * Scope a query to apply dynamic filters.
     */
    public function scopeFilter(Builder $query, array $filters): void
    {
        $query->when($filters['search'] ?? null, function (Builder $query, $search) {
            $query->where('name', 'like', "%{$search}%");
        });
    }
}
