<?php

namespace App\Http\Resources\Tenant\CMS;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BlogCommentResource extends JsonResource
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
             * The unique identifier for the blog comment.
             *
             * @var int $id
             *
             * @example 42
             */
            'id' => $this->id,

            /**
             * The ID of the blog post this comment belongs to.
             *
             * @var int $post_id
             *
             * @example 10
             */
            'post_id' => $this->blog_post_id,

            /**
             * The display name of the comment author.
             *
             * @var string $author_name
             *
             * @example "Jane Doe"
             */
            'author_name' => $this->customer?->name ?? $this->author_name,

            /**
             * The content body of the comment.
             *
             * @var string $body
             *
             * @example "This is a fantastic article! Thanks for sharing."
             */
            'body' => $this->body,

            /**
             * Indicates if the comment has been approved by a moderator.
             *
             * @var bool $is_approved
             *
             * @example true
             */
            'is_approved' => (bool) $this->is_approved,

            /**
             * The ISO-8601 formatted date and time when the comment was created.
             *
             * @var string|null $created_at
             *
             * @example "2026-04-25T09:45:00+00:00"
             */
            'created_at' => $this->created_at?->toIso8601String(),
        ];
    }
}
