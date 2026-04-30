<?php

namespace App\Models\Tenant\CMS;

use App\Traits\Auditable;
use App\Traits\HasTenantMedia;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;
use Spatie\MediaLibrary\HasMedia;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;

/**
 * Class Page
 *
 * Represents a static content page within the CMS.
 *
 * @property int $id The unique identifier of the page.
 * @property string $title The title of the page.
 * @property string $slug The URL-friendly version of the page title.
 * @property string|null $content The main body content.
 * @property array|null $blocks JSON structured content blocks.
 * @property string|null $seo_title The meta title for SEO.
 * @property string|null $seo_description The meta description for SEO.
 * @property bool $is_published Indicates if the page is visible to the public.
 * @property Carbon|null $published_at Timestamp when the page was published.
 * @property Carbon|null $created_at Timestamp of when the page was created.
 * @property Carbon|null $updated_at Timestamp of when the page was last updated.
 * @property Carbon|null $deleted_at Timestamp of soft deletion.
 *
 * @method static Builder filter(array $filters)
 * @method static Builder published()
 */
class Page extends Model implements AuditableContract, HasMedia
{
    use Auditable, HasSlug, HasTenantMedia, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'title',
        'slug',
        'content',
        'blocks',
        'seo_title',
        'seo_description',
        'is_published',
        'published_at',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'blocks' => 'array',
            'is_published' => 'bool',
            'published_at' => 'datetime',
        ];
    }

    /**
     * Get the options for generating the slug.
     */
    public function getSlugOptions(): SlugOptions
    {
        return SlugOptions::create()
            ->generateSlugsFrom('title')
            ->saveSlugsTo('slug');
    }

    /**
     * Scope a query to only include published pages.
     */
    public function scopePublished(Builder $query): void
    {
        $query->where('is_published', true);
    }

    /**
     * Scope a query to apply a dynamic array of filters.
     */
    public function scopeFilter(Builder $query, array $filters): void
    {
        $query->when($filters['search'] ?? null, function (Builder $query, string $search) {
            $query->where('title', 'like', "%{$search}%");
        })
            ->when(isset($filters['is_published']), function (Builder $query) use ($filters) {
                $query->where('is_published', $filters['is_published']);
            });
    }
}
