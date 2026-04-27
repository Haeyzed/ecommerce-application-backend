<?php

namespace App\Http\Requests\Tenant\Setting;

use Illuminate\Foundation\Http\FormRequest;

class UpdateMailSettingRequest extends FormRequest
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
             * The mail driver (e.g., smtp).
             * @var string $mailer
             * @example "smtp"
             */
            'mailer'       => ['sometimes', 'string', 'max:64'],

            /**
             * The mail server host.
             * @var string|null $host
             * @example "smtp.mailtrap.io"
             */
            'host'         => ['nullable', 'string', 'max:255'],

            /**
             * The mail server port.
             * @var int|null $port
             * @example 2525
             */
            'port'         => ['nullable', 'integer'],

            /**
             * The mail server username.
             * @var string|null $username
             * @example "my_smtp_user"
             */
            'username'     => ['nullable', 'string', 'max:255'],

            /**
             * The mail server password. Left null to keep existing.
             * @var string|null $password
             * @example "super_secret_smtp_pass"
             */
            'password'     => ['nullable', 'string', 'max:255'],

            /**
             * The encryption protocol (tls, ssl).
             * @var string|null $encryption
             * @example "tls"
             */
            'encryption'   => ['nullable', 'in:tls,ssl'],

            /**
             * The email address sending the emails.
             * @var string $from_address
             * @example "noreply@mystore.com"
             */
            'from_address' => ['sometimes', 'email', 'max:255'],

            /**
             * The name attached to outgoing emails.
             * @var string $from_name
             * @example "My Awesome Store"
             */
            'from_name'    => ['sometimes', 'string', 'max:255'],
        ];
    }
}
