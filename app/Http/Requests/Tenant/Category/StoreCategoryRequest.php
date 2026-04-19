<?php

namespace App\Http\Requests\Tenant\Category;

use Illuminate\Foundation\Http\FormRequest;

/**
 * @property string $name The name of the category.
 * @property string|null $slug The URL-friendly slug.
 */
class StoreCategoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', 'unique:categories,slug'],
        ];
    }
}
