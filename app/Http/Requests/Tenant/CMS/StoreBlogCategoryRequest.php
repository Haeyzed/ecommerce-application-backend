<?php

namespace App\Http\Requests\Tenant\CMS;

use Illuminate\Foundation\Http\FormRequest;

/**
 * @property string|null $name
 * @property string|null $description
 */
class StoreBlogCategoryRequest extends FormRequest
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
             * The name of the category.
             * @var string $name
             * @example "Technology"
             */
            'name'        => ['sometimes', 'string', 'max:255'],

            /**
             * A detailed description of the category.
             * @var string|null $description
             * @example "Posts related to modern software development."
             */
            'description' => ['sometimes', 'nullable', 'string'],
        ];
    }
}
