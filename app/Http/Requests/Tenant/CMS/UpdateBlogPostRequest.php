<?php

namespace App\Http\Requests\Tenant\CMS;

use Illuminate\Foundation\Http\FormRequest;

class UpdateBlogPostRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     * * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     * * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            /**
             * The ID of the associated blog category.
             * @var int|null $blog_category_id
             * @example 10
             */
            'blog_category_id' => ['sometimes', 'nullable', 'integer', 'exists:blog_categories,id'],

            /**
             * The updated title of the post.
             * @var string|null $title
             * @example "Updated Title"
             */
            'title'            => ['sometimes', 'string', 'max:255'],

            /**
             * The updated excerpt.
             * @var string|null $excerpt
             * @example "A newer summary."
             */
            'excerpt'          => ['sometimes', 'nullable', 'string', 'max:500'],

            /**
             * The updated content body.
             * @var string|null $content
             * @example "Full updated HTML or Markdown content."
             */
            'content'          => ['sometimes', 'nullable', 'string'],

            /**
             * The updated cover image path.
             * @var string|null $cover_image
             * @example "uploads/images/new-cover.png"
             */
            'cover_image'      => ['sometimes', 'nullable', 'string'],

            /**
             * Updated list of tags.
             * @var array|null $tags
             * @example ["new", "tags"]
             */
            'tags'             => ['sometimes', 'nullable', 'array'],

            /**
             * Individual tag validation.
             * @var string $tags
             * @example "php"
             */
            'tags.*'           => ['string', 'max:50'],

            /**
             * Updated SEO title.
             * @var string|null $seo_title
             * @example "Optimized SEO Title"
             */
            'seo_title'        => ['sometimes', 'nullable', 'string', 'max:255'],

            /**
             * Updated SEO description.
             * @var string|null $seo_description
             * @example "Meta description for SEO."
             */
            'seo_description'  => ['sometimes', 'nullable', 'string', 'max:500'],

            /**
             * Updated publication status.
             * @var bool|null $is_published
             * @example false
             */
            'is_published'     => ['sometimes', 'boolean'],

            /**
             * Updated publication date.
             * @var string|null $pulished_at
             * @example "2026-12-31 23:59:59"
             */
            'published_at'     => ['sometimes', 'nullable', 'date'],
        ];
    }
}
