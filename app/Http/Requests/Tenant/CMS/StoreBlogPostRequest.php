<?php

namespace App\Http\Requests\Tenant\CMS;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

/**
 * @property int|null $blog_category_id
 * @property string $title
 * @property string|null $excerpt
 * @property string|null $content
 * @property string|null $cover_image
 * @property array|null $tags
 * @property string|null $seo_title
 * @property string|null $seo_description
 * @property bool $is_published
 * @property string|null $published_at
 */
class StoreBlogPostRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            /**
             * The ID of the associated blog category.
             *
             * @var int|null $blog_category_id
             *
             * @example 5
             */
            'blog_category_id' => ['nullable', 'integer', 'exists:blog_categories,id'],

            /**
             * The title of the blog post.
             *
             * @var string $title
             *
             * @example "How to learn Laravel in 2026"
             */
            'title' => ['required', 'string', 'max:255'],

            /**
             * A short summary of the post.
             *
             * @var string|null $excerpt
             *
             * @example "An introductory guide to Laravel's latest features."
             */
            'excerpt' => ['nullable', 'string', 'max:500'],

            /**
             * The main content of the post.
             *
             * @var string|null $content
             *
             * @example "<p>Laravel is a web application framework...</p>"
             */
            'content' => ['nullable', 'string'],

            /**
             * The URL or path to the cover image.
             *
             * @var string|null $cover_image
             *
             * @example "https://example.com/images/cover.jpg"
             */
            'cover_image' => ['nullable', 'string'],

            /**
             * List of tags associated with the post.
             *
             * @var array|null $tags
             *
             * @example ["php", "laravel", "backend"]
             */
            'tags' => ['nullable', 'array'],

            /**
             * Individual tag validation.
             *
             * @var string $tags
             *
             * @example "laravel"
             */
            'tags.*' => ['string', 'max:50'],

            /**
             * SEO optimized title for the post.
             *
             * @var string|null $seo_title
             *
             * @example "Learn Laravel 2026 | Ultimate Guide"
             */
            'seo_title' => ['nullable', 'string', 'max:255'],

            /**
             * SEO meta description.
             *
             * @var string|null $seo_description
             *
             * @example "Comprehensive guide on learning Laravel with modern practices."
             */
            'seo_description' => ['nullable', 'string', 'max:500'],

            /**
             * The publication status of the post.
             *
             * @var bool $is_published
             *
             * @example true
             */
            'is_published' => ['boolean'],

            /**
             * The scheduled date and time of publication.
             *
             * @var string|null $published_at
             *
             * @example "2026-04-25 10:00:00"
             */
            'published_at' => ['nullable', 'date'],
        ];
    }
}
