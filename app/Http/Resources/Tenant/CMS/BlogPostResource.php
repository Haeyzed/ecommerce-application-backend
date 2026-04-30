<?php

namespace App\Http\Resources\Tenant\CMS;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BlogPostResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            /**
             * The unique identifier for the blog post.
             *
             * @var int $id
             *
             * @example 15
             */
            'id' => $this->id,

            /**
             * The title of the blog post.
             *
             * @var string $title
             *
             * @example "Top 10 Laravel Tips for 2026"
             */
            'title' => $this->title,

            /**
             * The URL-friendly slug for the blog post.
             *
             * @var string $slug
             *
             * @example "top-10-laravel-tips-2026"
             */
            'slug' => $this->slug,

            /**
             * A short summary or excerpt of the blog post.
             *
             * @var string|null $excerpt
             *
             * @example "Discover the best practices for Laravel development this year."
             */
            'excerpt' => $this->excerpt,

            /**
             * The main content of the blog post (included on show route or when requested).
             *
             * @var string|null $content
             *
             * @example "<p>Laravel continues to evolve...</p>"
             */
            'content' => $this->when($request->routeIs('*.show') || $request->boolean('full'), $this->content),

            /**
             * The relative path or identifier for the cover image.
             *
             * @var string|null $cover_image
             *
             * @example "posts/covers/laravel-tips.png"
             */
            'cover_image' => $this->cover_image,

            /**
             * The fully qualified URL to the blog post's cover image.
             *
             * @var string|null $cover_url
             *
             * @example "https://example.com/media/laravel-tips.png"
             */
            'cover_url' => method_exists($this->resource, 'getFirstMediaUrl') ? $this->getFirstMediaUrl('default') : null,

            /**
             * An array of tags associated with the post.
             *
             * @var array|null $tags
             *
             * @example ["laravel", "php", "webdev"]
             */
            'tags' => $this->tags,

            /**
             * The ID of the category this post belongs to.
             *
             * @var int|null $category_id
             *
             * @example 3
             */
            'category_id' => $this->blog_category_id,

            /**
             * The ID of the author who wrote the post.
             *
             * @var int|null $author_id
             *
             * @example 7
             */
            'author_id' => $this->author_id,

            /**
             * Indicates if the blog post is currently published.
             *
             * @var bool $is_published
             *
             * @example true
             */
            'is_published' => (bool) $this->is_published,

            /**
             * The ISO-8601 formatted date and time when the post was published.
             *
             * @var string|null $published_at
             *
             * @example "2026-04-01T12:00:00+00:00"
             */
            'published_at' => optional($this->published_at)->toIso8601String(),

            /**
             * The total number of views the post has received.
             *
             * @var int $views
             *
             * @example 1542
             */
            'views' => (int) $this->views,

            /**
             * The SEO-optimized title for the post.
             *
             * @var string|null $seo_title
             *
             * @example "10 Essential Laravel Tips for 2026"
             */
            'seo_title' => $this->seo_title,

            /**
             * The SEO meta description for the post.
             *
             * @var string|null $seo_description
             *
             * @example "A comprehensive guide covering the top 10 tips for Laravel developers."
             */
            'seo_description' => $this->seo_description,

            /**
             * The ISO-8601 formatted date and time when the post was created.
             *
             * @var string|null $created_at
             *
             * @example "2026-03-25T14:30:00+00:00"
             */
            'created_at' => $this->created_at?->toIso8601String(),
        ];
    }
}
