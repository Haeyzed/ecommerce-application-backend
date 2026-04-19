<?php

namespace App\Http\Resources\Central;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property int $id The unique identifier of the user. @example 1
 * @property string $name The full name of the user. @example Victor Ugwu
 * @property string $email The email address of the user. @example victor@example.com
 * @property string|null $email_verified_at ISO 8601 timestamp of when the email was verified. @example 2026-04-17T22:30:00Z
 * @property string $created_at ISO 8601 timestamp of when the account was created. @example 2026-04-17T22:00:00Z
 */
class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'email_verified_at' => $this->email_verified_at ? $this->email_verified_at->toIso8601String() : null,
            'created_at' => $this->created_at->toIso8601String(),
        ];
    }
}
