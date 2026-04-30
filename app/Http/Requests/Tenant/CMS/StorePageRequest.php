<?php

namespace App\Http\Requests\Tenant\CMS;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StorePageRequest extends FormRequest
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
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            /**
             * The title of the page.
             *
             * @var string $title
             *
             * @example "About Us"
             */
            'title' => ['required', 'string', 'max:255'],

            /**
             * The URL-friendly slug for the page.
             *
             * @var string|null $slug
             *
             * @example "about-us"
             */
            'slug' => ['nullable', 'string', 'max:255'],

            /**
             * The main HTML or rich-text content of the page.
             *
             * @var string|null $content
             *
             * @example "<h1>Welcome to our company</h1>"
             */
            'content' => ['nullable', 'string'],

            /**
             * An array of structured blocks for dynamic page builders.
             *
             * @var array|null $blocks
             *
             * @example [{"type": "hero", "data": {"heading": "Hello"}}]
             */
            'blocks' => ['nullable', 'array'],

            /**
             * The SEO title for the page.
             *
             * @var string|null $seo_title
             *
             * @example "About Us | Our Company"
             */
            'seo_title' => ['nullable', 'string', 'max:255'],

            /**
             * The SEO meta description for the page.
             *
             * @var string|null $seo_description
             *
             * @example "Learn more about our company and values."
             */
            'seo_description' => ['nullable', 'string', 'max:500'],

            /**
             * Indicates if the page should be published immediately.
             *
             * @var bool $is_published
             *
             * @example true
             */
            'is_published' => ['boolean'],

            /**
             * The specific date and time when the page should be published.
             *
             * @var string|null $published_at
             *
             * @example "2026-05-01 10:00:00"
             */
            'published_at' => ['nullable', 'date'],
        ];
    }
}
