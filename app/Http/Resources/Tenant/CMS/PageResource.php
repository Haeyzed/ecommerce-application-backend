<?php

namespace App\Http\Resources\Tenant\CMS;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PageResource extends JsonResource
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
             * The unique identifier for the page.
             *
             * @var int $id
             *
             * @example 1
             */
            'id' => $this->id,

            /**
             * The title of the page.
             *
             * @var string $title
             *
             * @example "About Us"
             */
            'title' => $this->title,

            /**
             * The URL-friendly slug for the page.
             *
             * @var string $slug
             *
             * @example "about-us"
             */
            'slug' => $this->slug,

            /**
             * The main HTML or rich-text content of the page.
             *
             * @var string|null $content
             *
             * @example "<h1>Welcome to our company</h1>"
             */
            'content' => $this->content,

            /**
             * An array of structured blocks for dynamic page builders.
             *
             * @var array|null $blocks
             *
             * @example [{"type": "hero", "data": {"heading": "Hello"}}]
             */
            'blocks' => $this->blocks,

            /**
             * The SEO title for the page.
             *
             * @var string|null $seo_title
             *
             * @example "About Us | Our Company"
             */
            'seo_title' => $this->seo_title,

            /**
             * The SEO meta description for the page.
             *
             * @var string|null $seo_description
             *
             * @example "Learn more about our company and values."
             */
            'seo_description' => $this->seo_description,

            /**
             * Indicates if the page is currently published.
             *
             * @var bool $is_published
             *
             * @example true
             */
            'is_published' => (bool) $this->is_published,

            /**
             * The ISO-8601 formatted date and time when the page was published.
             *
             * @var string|null $published_at
             *
             * @example "2026-05-01T10:00:00+00:00"
             */
            'published_at' => optional($this->published_at)->toIso8601String(),

            /**
             * The full URL to the page's cover image.
             *
             * @var string|null $cover_url
             *
             * @example "https://example.com/media/cover.jpg"
             */
            'cover_url' => method_exists($this->resource, 'getFirstMediaUrl') ? $this->getFirstMediaUrl('default') : null,

            /**
             * The ISO-8601 formatted date and time when the page was created.
             *
             * @var string|null $created_at
             *
             * @example "2026-04-20T08:30:00+00:00"
             */
            'created_at' => $this->created_at?->toIso8601String(),

            /**
             * The ISO-8601 formatted date and time when the page was last updated.
             *
             * @var string|null $updated_at
             *
             * @example "2026-04-25T14:15:00+00:00"
             */
            'updated_at' => $this->updated_at?->toIso8601String(),
        ];
    }
}
