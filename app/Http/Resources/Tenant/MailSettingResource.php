<?php

namespace App\Http\Resources\Tenant;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MailSettingResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            /**
             * The mail driver (e.g., smtp).
             *
             * @var string $mailer
             *
             * @example "smtp"
             */
            'mailer' => $this->mailer,

            /**
             * The mail server host.
             *
             * @var string|null $host
             *
             * @example "smtp.mailtrap.io"
             */
            'host' => $this->host,

            /**
             * The mail server port.
             *
             * @var int|null $port
             *
             * @example 2525
             */
            'port' => $this->port,

            /**
             * The mail server username.
             *
             * @var string|null $username
             *
             * @example "my_smtp_user"
             */
            'username' => $this->username,

            /**
             * The encryption protocol (tls, ssl).
             *
             * @var string|null $encryption
             *
             * @example "tls"
             */
            'encryption' => $this->encryption,

            /**
             * The email address sending the emails.
             *
             * @var string $from_address
             *
             * @example "noreply@mystore.com"
             */
            'from_address' => $this->from_address,

            /**
             * The name attached to outgoing emails.
             *
             * @var string $from_name
             *
             * @example "My Awesome Store"
             */
            'from_name' => $this->from_name,

            /**
             * Boolean indicating if a password is currently saved in the database.
             *
             * @var bool $has_password
             *
             * @example true
             */
            'has_password' => ! empty($this->password),
        ];
    }
}
