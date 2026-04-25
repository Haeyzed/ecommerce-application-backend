<?php

namespace App\Http\Requests\Central\Auth;

use Illuminate\Foundation\Http\FormRequest;

/**
 * @property string $email The user's registered email address.
 * @property string $password The user's password.
 */
class LoginRequest extends FormRequest
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
             * The user's registered email address.
             * @var string $email
             * @example "victor@example.com"
             */
            'email' => ['required', 'string', 'email'],

            /**
             * The user's password.
             * @var string $password
             * @example "SecretP@ssw0rd!"
             */
            'password' => ['required', 'string'],
        ];
    }
}
