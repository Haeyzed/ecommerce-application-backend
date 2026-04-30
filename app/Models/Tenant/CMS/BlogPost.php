<?php

namespace App\Models\Tenant\CMS;

use App\Models\Tenant\Staff;
use App\Traits\Auditable;
use App\Traits\HasTenantMedia;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;
use Spatie\MediaLibrary\HasMedia;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;

/**
 * Class BlogPost
 *
 * Represents an article or entry in the blog system.
 *
 * @property int $id The unique identifier of the post.
 * @property int|null $blog_category_id The foreign key for the category.
 * @property int|null $staff_id The foreign key for the staff author.
 * @property string $title The title of the post.
 * @property string $slug The URL-friendly version of the post title.
 * @property string|null $excerpt A short summary of the post.
 * @property string|null $content The main body of the article.
 * @property string|null $cover_image Path or URL to the post's featured image.
 * @property array|null $tags Array of tags associated with the post.
 * @property string|null $seo_title The meta title for SEO.
 * @property string|null $seo_description The meta description for SEO.
 * @property bool $is_published Indicates if the post is live.
 * @property Carbon|null $published_at Timestamp when the post was published.
 * @property int $views Total number of times the post has been viewed.
 * @property Carbon|null $created_at Timestamp of when the post was created.
 * @property Carbon|null $updated_at Timestamp of when the post was last updated.
 * @property Carbon|null $deleted_at Timestamp of soft deletion.
 * @property-read BlogCategory|null $category The category this post belongs to.
 * @property-read Staff|null $author The user who wrote the post.
 *
 * @method static Builder filter(array $filters)
 * @method static Builder published()
 */
class BlogPost extends Model implements AuditableContract, HasMedia
{
    use Auditable, HasSlug, HasTenantMedia, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'blog_category_id',
        'staff_id',
        'title',
        'slug',
        'excerpt',
        'content',
        'cover_image',
        'tags',
        'seo_title',
        'seo_description',
        'is_published',
        'published_at',
        'views',
        'customer_id',
        'author_name',
        'author_email',
        'body',
        'is_approved',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'tags' => 'array',
            'is_published' => 'bool',
            'published_at' => 'datetime',
            'views' => 'int',
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
     * Get the category that the post belongs to.
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(BlogCategory::class, 'blog_category_id');
    }

    /**
     * Get the staff user who authored the post.
     */
    public function staff(): BelongsTo
    {
        return $this->belongsTo(Staff::class, 'staff_id');
    }

    /**
     * Get the comments associated with this post.
     */
    public function comments(): HasMany
    {
        return $this->hasMany(BlogComment::class);
    }

    /**
     * Scope a query to only include published posts.
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
            ->when($filters['category_id'] ?? null, function (Builder $query, mixed $categoryId) {
                $query->where('blog_category_id', $categoryId);
            })
            ->when($filters['tag'] ?? null, function (Builder $query, mixed $tag) {
                $query->whereJsonContains('tags', $tag);
            })
            ->when(isset($filters['is_published']), function (Builder $query) use ($filters) {
                $query->where('is_published', $filters['is_published']);
            });
    }
}
