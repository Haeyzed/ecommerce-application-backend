<?php

namespace App\Http\Controllers\Tenant\Api\CMS;

use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\Tenant\CMS\StoreBlogCategoryRequest;
use App\Http\Requests\Tenant\CMS\StoreBlogCommentRequest;
use App\Http\Requests\Tenant\CMS\StoreBlogPostRequest;
use App\Http\Requests\Tenant\CMS\UpdateBlogCategoryRequest;
use App\Http\Requests\Tenant\CMS\UpdateBlogPostRequest;
use App\Http\Resources\Tenant\CMS\BlogCategoryResource;
use App\Http\Resources\Tenant\CMS\BlogCommentResource;
use App\Http\Resources\Tenant\CMS\BlogPostResource;
use App\Models\Tenant\CMS\BlogCategory;
use App\Models\Tenant\CMS\BlogComment;
use App\Models\Tenant\CMS\BlogPost;
use App\Services\Tenant\CMS\BlogService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Blog Endpoints
 * * Handles management of posts, categories, and moderation of comments.
 */
class BlogController extends Controller
{
    public function __construct(
        private readonly BlogService $blogService
    ) {}

    /**
     * List all blog posts (Admin/Public combined filters).
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $perPage = $request->integer('per_page', 20);
        $posts = $this->blogService->getPaginatedPosts($request->all(), $perPage);

        return ApiResponse::success(
            data: BlogPostResource::collection($posts),
            message: 'Posts retrieved successfully',
            meta: ApiResponse::meta($posts)
        );
    }

    /**
     * Show a specific blog post by slug.
     *
     * @param string $slug
     * @return JsonResponse
     */
    public function show(string $slug): JsonResponse
    {
        $post = $this->blogService->getPostBySlug($slug);
        $this->blogService->incrementViews($post);

        return ApiResponse::success(
            new BlogPostResource($post),
            'Post retrieved successfully'
        );
    }

    /**
     * Create a new blog post.
     *
     * @param StoreBlogPostRequest $request
     * @return JsonResponse
     */
    public function store(StoreBlogPostRequest $request): JsonResponse
    {
        $data = $request->validated();
        $data['author_id'] = $request->user('staff')?->id;

        $post = $this->blogService->createPost($data);

        return ApiResponse::success(
            new BlogPostResource($post),
            'Post created successfully',
            null,
            201
        );
    }

    /**
     * Update an existing blog post.
     *
     * @param UpdateBlogPostRequest $request
     * @param int $id
     * @return JsonResponse
     */
    public function update(UpdateBlogPostRequest $request, int $id): JsonResponse
    {
        $post = BlogPost::query()->findOrFail($id);
        $updatedPost = $this->blogService->updatePost($post, $request->validated());

        return ApiResponse::success(
            new BlogPostResource($updatedPost),
            'Post updated successfully'
        );
    }

    /**
     * Delete a blog post.
     *
     * @param int $id
     * @return JsonResponse
     */
    public function destroy(int $id): JsonResponse
    {
        $post = BlogPost::query()->findOrFail($id);
        $this->blogService->deletePost($post);

        return ApiResponse::success(null, 'Post deleted successfully');
    }

    /**
     * List all comments for a specific blog post.
     *
     * @param Request $request
     * @param string $slug
     * @return JsonResponse
     */
    public function getComments(Request $request, string $slug): JsonResponse
    {
        $post = $this->blogService->getPostBySlug($slug);

        // Default to only showing approved comments (safe for public view).
        // Passing ?is_approved=all or ?is_approved=0 will override this (useful for staff panels).
        $isApproved = true;

        if ($request->has('is_approved')) {
            $queryParam = $request->query('is_approved');
            $isApproved = $queryParam === 'all' ? null : filter_var($queryParam, FILTER_VALIDATE_BOOLEAN);
        }

        $comments = $this->blogService->getCommentsByPost($post->id, $isApproved);

        return ApiResponse::success(
            BlogCommentResource::collection($comments),
            'Comments retrieved successfully'
        );
    }

    /**
     * Post a comment on a blog post.
     *
     * @param StoreBlogCommentRequest $request
     * @param string $slug
     * @return JsonResponse
     */
    public function publicComment(StoreBlogCommentRequest $request, string $slug): JsonResponse
    {
        $post = $this->blogService->getPostBySlug($slug);
        $comment = $this->blogService->addComment($post, $request->validated(), $request->user('customer')?->id);

        return ApiResponse::success(
            new BlogCommentResource($comment),
            'Comment submitted for review',
            null,
            201
        );
    }

    /**
     * List all blog categories.
     *
     * @return JsonResponse
     */
    public function categoriesIndex(): JsonResponse
    {
        $categories = $this->blogService->getCategories();

        return ApiResponse::success(
            BlogCategoryResource::collection($categories),
            'Categories retrieved successfully'
        );
    }

    /**
     * Create a new blog category.
     *
     * @param StoreBlogCategoryRequest $request
     * @return JsonResponse
     */
    public function categoriesStore(StoreBlogCategoryRequest $request): JsonResponse
    {
        $category = $this->blogService->createCategory($request->validated());

        return ApiResponse::success(
            new BlogCategoryResource($category),
            'Category created successfully',
            null,
            201
        );
    }

    /**
     * Update a blog category.
     *
     * @param UpdateBlogCategoryRequest $request
     * @param int $id
     * @return JsonResponse
     */
    public function categoriesUpdate(UpdateBlogCategoryRequest $request, int $id): JsonResponse
    {
        $category = BlogCategory::query()->findOrFail($id);
        $updated = $this->blogService->updateCategory($category, $request->validated());

        return ApiResponse::success(
            new BlogCategoryResource($updated),
            'Category updated successfully'
        );
    }

    /**
     * Delete a blog category.
     *
     * @param int $id
     * @return JsonResponse
     */
    public function categoriesDestroy(int $id): JsonResponse
    {
        $category = BlogCategory::query()->findOrFail($id);
        $this->blogService->deleteCategory($category);

        return ApiResponse::success(null, 'Category deleted successfully');
    }

    /**
     * Approve or update a comment (Moderation).
     *
     * @param Request $request
     * @param int $id
     * @return JsonResponse
     */
    public function updateComment(Request $request, int $id): JsonResponse
    {
        $comment = BlogComment::query()->findOrFail($id);
        $updated = $this->blogService->updateComment($comment, $request->only('is_approved', 'body'));

        return ApiResponse::success(
            ['comment' => new BlogCommentResource($updated)],
            'Comment updated successfully'
        );
    }

    /**
     * Delete a comment.
     *
     * @param int $id
     * @return JsonResponse
     */
    public function destroyComment(int $id): JsonResponse
    {
        $comment = BlogComment::query()->findOrFail($id);
        $this->blogService->deleteComment($comment);

        return ApiResponse::success(null, 'Comment deleted successfully');
    }
}
