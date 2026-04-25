<?php

namespace App\Http\Resources\Tenant;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            /**
             * The unique identifier of the user.
             * @var int $id
             * @example 1
             */
            'id' => $this->id,

            /**
             * The full name of the user.
             * @var string $name
             * @example "Victor Ugwu"
             */
            'name' => $this->name,

            /**
             * The email address of the user.
             * @var string $email
             * @example "victor@example.com"
             */
            'email' => $this->email,

            /**
             * ISO 8601 timestamp of when the email was verified.
             * @var string|null $email_verified_at
             * @example "2026-04-17T22:30:00Z"
             */
            'email_verified_at' => $this->email_verified_at ? $this->email_verified_at->toIso8601String() : null,

            /**
             * ISO 8601 timestamp of when the account was created.
             * @var string $created_at
             * @example "2026-04-17T22:00:00Z"
             */
            'created_at' => $this->created_at->toIso8601String(),
        ];
    }
}
