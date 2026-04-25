<?php

namespace App\Http\Requests\Central\Auth;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

/**
 * @property string $name The full name of the user.
 * @property string $email The email address of the user.
 * @property string $password The desired password.
 * @property string $password_confirmation The password confirmation.
 */
class RegisterRequest extends FormRequest
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
             * The full name of the user.
             * @var string $name
             * @example "Victor Ugwu"
             */
            'name' => ['required', 'string', 'min:2', 'max:50'],

            /**
             * The email address of the user.
             * @var string $email
             * @example "victor@example.com"
             */
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],

            /**
             * The desired password (minimum 8 characters).
             * @var string $password
             * @example "SecretP@ssw0rd!"
             */
            'password' => ['required', 'confirmed', Password::min(8)],
        ];
    }
}
