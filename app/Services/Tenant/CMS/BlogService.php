<?php

namespace App\Services\Tenant\CMS;

use App\Models\Tenant\CMS\BlogCategory;
use App\Models\Tenant\CMS\BlogComment;
use App\Models\Tenant\CMS\BlogPost;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

/**
 * Class BlogService
 * * Handles business logic related to tenant blog posts, categories, and comments.
 */
class BlogService
{
    /**
     * Retrieve a paginated, filtered list of blog posts.
     *
     * @param  array  $filters  Query filters (e.g., category_id, search, is_published, tag)
     * @param  int  $perPage  Items per page
     */
    public function getPaginatedPosts(array $filters = [], int $perPage = 20): LengthAwarePaginator
    {
        return BlogPost::query()
            ->filter($filters)
            ->latest('published_at')
            ->paginate($perPage);
    }

    /**
     * Retrieve a specific published post by its slug.
     */
    public function getPostBySlug(string $slug): BlogPost
    {
        return BlogPost::query()
            ->published()
            ->where('slug', $slug)
            ->firstOrFail();
    }

    /**
     * Create a new blog post.
     *
     * @param  array  $data  Validated post data.
     */
    public function createPost(array $data): BlogPost
    {
        return BlogPost::query()->create($data);
    }

    /**
     * Update an existing blog post.
     *
     * @param  array  $data  Validated update data.
     */
    public function updatePost(BlogPost $post, array $data): BlogPost
    {
        $post->update($data);

        return $post->fresh();
    }

    /**
     * Delete a blog post.
     */
    public function deletePost(BlogPost $post): void
    {
        $post->delete();
    }

    /**
     * Increment the view counter for a post.
     */
    public function incrementViews(BlogPost $post): void
    {
        $post->increment('views');
    }

    /**
     * Get all categories with post counts, optionally filtered.
     *
     * @param  array  $filters  Query filters (e.g., search)
     */
    public function getCategories(array $filters = []): Collection
    {
        return BlogCategory::query()
            ->filter($filters)
            ->withCount('posts')
            ->orderBy('name')
            ->get();
    }

    /**
     * Create a new blog category.
     */
    public function createCategory(array $data): BlogCategory
    {
        return BlogCategory::query()->create($data);
    }

    /**
     * Update an existing blog category.
     */
    public function updateCategory(BlogCategory $category, array $data): BlogCategory
    {
        $category->update($data);

        return $category->refresh();
    }

    /**
     * Delete a blog category.
     */
    public function deleteCategory(BlogCategory $category): void
    {
        $category->delete();
    }

    /**
     * Retrieve all comments for a post, optionally filtered.
     *
     * @param  array  $filters  Query filters (e.g., is_approved, search)
     */
    public function getCommentsByPost(int $postId, array $filters = []): Collection
    {
        return BlogComment::query()
            ->where('blog_post_id', $postId)
            ->filter($filters)
            ->latest()
            ->get();
    }

    /**
     * Add a comment to a specific post.
     *
     * @param  array  $data  Validated comment data.
     */
    public function addComment(BlogPost $post, array $data, ?int $customerId = null): BlogComment
    {
        return $post->comments()->create([
            'customer_id' => $customerId,
            'author_name' => $data['author_name'] ?? null,
            'author_email' => $data['author_email'] ?? null,
            'body' => $data['body'],
            'is_approved' => false,
        ]);
    }

    /**
     * Update a comment (e.g., moderation/approval).
     */
    public function updateComment(BlogComment $comment, array $data): BlogComment
    {
        $comment->update($data);

        return $comment->refresh();
    }

    /**
     * Delete a comment.
     */
    public function deleteComment(BlogComment $comment): void
    {
        $comment->delete();
    }
}
