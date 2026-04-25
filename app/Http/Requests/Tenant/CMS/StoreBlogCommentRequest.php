<?php

namespace App\Http\Requests\Tenant\CMS;

use Illuminate\Foundation\Http\FormRequest;

class StoreBlogCommentRequest extends FormRequest
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
             * The name of the comment author.
             * @var string|null $author_name
             * @example "John Doe"
             */
            'author_name'  => ['nullable', 'string', 'max:120'],

            /**
             * The email address of the author.
             * @var string|null $author_email
             * @example "john@example.com"
             */
            'author_email' => ['nullable', 'email'],

            /**
             * The content of the comment.
             * @var string $body
             * @example "Great post! Thanks for sharing."
             */
            'body'         => ['required', 'string', 'max:5000'],
        ];
    }
}
