<?php

namespace App\Http\Requests\Central\Auth;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

/**
 * @property string $token The reset token received via email.
 * @property string $email The user's email address.
 * @property string $password The new password.
 * @property string $password_confirmation The new password confirmation.
 */
class ResetPasswordRequest extends FormRequest
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
             * The reset token received via email.
             *
             * @var string $token
             *
             * @example "9b7a4f3a..."
             */
            'token' => ['required'],

            /**
             * The user's email address.
             *
             * @var string $email
             *
             * @example "victor@example.com"
             */
            'email' => ['required', 'string', 'email'],

            /**
             * The new password.
             *
             * @var string $password
             *
             * @example "NewSecretP@ssw0rd!"
             */
            'password' => ['required', 'confirmed', Password::min(8)],
        ];
    }
}
