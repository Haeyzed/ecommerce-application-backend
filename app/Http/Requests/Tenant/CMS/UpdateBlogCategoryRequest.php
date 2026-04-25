<?php

namespace App\Http\Requests\Tenant\CMS;

use Illuminate\Foundation\Http\FormRequest;

/**
 * @property string|null $name
 * @property string|null $description
 */
class UpdateBlogCategoryRequest extends FormRequest
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
             * The updated name of the category.
             * @var string|null $name
             * @example "Updated Technology"
             */
            'name'        => ['sometimes', 'string', 'max:255'],

            /**
             * The updated description of the category.
             * @var string|null $description
             * @example "Updated description for this category."
             */
            'description' => ['sometimes', 'nullable', 'string'],
        ];
    }
}
