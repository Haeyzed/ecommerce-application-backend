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
     * @param array $filters Query filters (e.g., category_id, search, is_published, tag)
     * @param int $perPage Items per page
     * @return LengthAwarePaginator
     */
    public function getPaginatedPosts(array $filters = [], int $perPage = 20): LengthAwarePaginator
    {
        return BlogPost::query()
            ->when($filters['search'] ?? null, fn($q, $v) => $q->where('title', 'like', "%{$v}%"))
            ->when($filters['category_id'] ?? null, fn($q, $v) => $q->where('blog_category_id', $v))
            ->when($filters['tag'] ?? null, fn($q, $v) => $q->whereJsonContains('tags', $v))
            ->when(isset($filters['is_published']), fn($q) => $q->where('is_published', (bool)$filters['is_published']))
            ->latest('published_at')
            ->paginate($perPage);
    }

    /**
     * Retrieve a specific published post by its slug.
     *
     * @param string $slug
     * @return BlogPost
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
     * @param array $data Validated post data.
     * @return BlogPost
     */
    public function createPost(array $data): BlogPost
    {
        return BlogPost::query()->create($data);
    }

    /**
     * Update an existing blog post.
     *
     * @param BlogPost $post
     * @param array $data Validated update data.
     * @return BlogPost
     */
    public function updatePost(BlogPost $post, array $data): BlogPost
    {
        $post->update($data);
        return $post->fresh();
    }

    /**
     * Delete a blog post.
     *
     * @param BlogPost $post
     * @return void
     */
    public function deletePost(BlogPost $post): void
    {
        $post->delete();
    }

    /**
     * Increment the view counter for a post.
     *
     * @param BlogPost $post
     * @return void
     */
    public function incrementViews(BlogPost $post): void
    {
        $post->increment('views');
    }

    /**
     * Get all categories with post counts.
     *
     * @return Collection
     */
    public function getCategories(): Collection
    {
        return BlogCategory::query()
            ->withCount('posts')
            ->orderBy('name')
            ->get();
    }

    /**
     * Create a new blog category.
     *
     * @param array $data
     * @return BlogCategory
     */
    public function createCategory(array $data): BlogCategory
    {
        return BlogCategory::query()->create($data);
    }

    /**
     * Update an existing blog category.
     *
     * @param BlogCategory $category
     * @param array $data
     * @return BlogCategory
     */
    public function updateCategory(BlogCategory $category, array $data): BlogCategory
    {
        $category->update($data);
        return $category->refresh();
    }

    /**
     * Delete a blog category.
     *
     * @param BlogCategory $category
     * @return void
     */
    public function deleteCategory(BlogCategory $category): void
    {
        $category->delete();
    }

    /**
     * Retrieve all comments for a post, optionally filtered by approval status.
     *
     * @param int $postId
     * @param bool|null $isApproved
     * @return Collection
     */
    public function getCommentsByPost(int $postId, ?bool $isApproved = null): Collection
    {
        return BlogComment::query()
            ->where('blog_post_id', $postId)
            ->when(!is_null($isApproved), fn($q) => $q->where('is_approved', $isApproved))
            ->latest()
            ->get();
    }

    /**
     * Add a comment to a specific post.
     *
     * @param BlogPost $post
     * @param array $data Validated comment data.
     * @param int|null $customerId
     * @return BlogComment
     */
    public function addComment(BlogPost $post, array $data, ?int $customerId = null): BlogComment
    {
        return $post->comments()->create([
            'customer_id'  => $customerId,
            'author_name'  => $data['author_name']  ?? null,
            'author_email' => $data['author_email'] ?? null,
            'body'         => $data['body'],
            'is_approved'  => false,
        ]);
    }

    /**
     * Update a comment (e.g., moderation/approval).
     *
     * @param BlogComment $comment
     * @param array $data
     * @return BlogComment
     */
    public function updateComment(BlogComment $comment, array $data): BlogComment
    {
        $comment->update($data);
        return $comment->refresh();
    }

    /**
     * Delete a comment.
     *
     * @param BlogComment $comment
     * @return void
     */
    public function deleteComment(BlogComment $comment): void
    {
        $comment->delete();
    }
}
