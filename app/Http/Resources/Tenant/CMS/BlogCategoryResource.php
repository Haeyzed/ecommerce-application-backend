<?php

namespace App\Http\Resources\Tenant\CMS;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BlogCategoryResource extends JsonResource
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
             * The unique identifier for the blog category.
             *
             * @var int $id
             *
             * @example 3
             */
            'id' => $this->id,

            /**
             * The name of the category.
             *
             * @var string $name
             *
             * @example "Web Development"
             */
            'name' => $this->name,

            /**
             * The URL-friendly slug for the category.
             *
             * @var string $slug
             *
             * @example "web-development"
             */
            'slug' => $this->slug,

            /**
             * A detailed description of what the category encompasses.
             *
             * @var string|null $description
             *
             * @example "Articles and tutorials related to modern web development."
             */
            'description' => $this->description,

            /**
             * The total number of posts associated with this category (if loaded).
             *
             * @var int|null $posts_count
             *
             * @example 24
             */
            'posts_count' => $this->whenCounted('posts'),
        ];
    }
}
