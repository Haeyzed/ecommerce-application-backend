<?php

namespace App\Http\Requests\Tenant\CMS;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePageRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            /**
             * The updated title of the page.
             * @var string|null $title
             * @example "Updated About Us"
             */
            'title'           => ['sometimes', 'string', 'max:255'],

            /**
             * The updated URL-friendly slug for the page.
             * @var string|null $slug
             * @example "updated-about-us"
             */
            'slug'            => ['sometimes', 'nullable', 'string', 'max:255'],

            /**
             * The updated HTML or rich-text content of the page.
             * @var string|null $content
             * @example "<h1>Updated Company Info</h1>"
             */
            'content'         => ['sometimes', 'nullable', 'string'],

            /**
             * An updated array of structured blocks for dynamic page builders.
             * @var array|null $blocks
             * @example [{"type": "hero", "data": {"heading": "Welcome Back"}}]
             */
            'blocks'          => ['sometimes', 'nullable', 'array'],

            /**
             * The updated SEO title for the page.
             * @var string|null $seo_title
             * @example "About Us | Updated 2026"
             */
            'seo_title'       => ['sometimes', 'nullable', 'string', 'max:255'],

            /**
             * The updated SEO meta description for the page.
             * @var string|null $seo_description
             * @example "Read our newly updated company guidelines and history."
             */
            'seo_description' => ['sometimes', 'nullable', 'string', 'max:500'],

            /**
             * Updated publication status.
             * @var bool|null $is_published
             * @example true
             */
            'is_published'    => ['sometimes', 'boolean'],

            /**
             * Updated scheduled publication date and time.
             * @var string|null $published_at
             * @example "2026-06-01 12:00:00"
             */
            'published_at'    => ['sometimes', 'nullable', 'date'],
        ];
    }
}
