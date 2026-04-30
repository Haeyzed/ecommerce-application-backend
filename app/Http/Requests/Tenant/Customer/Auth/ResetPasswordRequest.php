<?php

namespace App\Http\Requests\Tenant\Customer\Auth;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

/**
 * @property string $token The reset token received via email. @example 9b7...f3a
 * @property string $email The user's email address. @example victor@example.com
 * @property string $password The new password. @example NewSecretP@ssw0rd!
 * @property string $password_confirmation The new password confirmation. @example NewSecretP@ssw0rd!
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
            'token' => ['required'],
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'confirmed', Password::min(8)],
        ];
    }
}
