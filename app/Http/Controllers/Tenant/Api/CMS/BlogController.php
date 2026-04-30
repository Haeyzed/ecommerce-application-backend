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
     */
    public function index(Request $request): JsonResponse
    {
        $perPage = $request->integer('per_page', 20);

        $filters = [
            'search' => $request->string('search'),
            'category_id' => $request->integer('category_id'),
            'tag' => $request->string('tag'),
        ];

        if ($request->has('is_published')) {
            $filters['is_published'] = $request->boolean('is_published');
        }

        $posts = $this->blogService->getPaginatedPosts($filters, $perPage);

        return ApiResponse::success(
            data: BlogPostResource::collection($posts),
            message: 'Posts retrieved successfully',
            meta: ApiResponse::meta($posts)
        );
    }

    /**
     * Show a specific blog post by slug.
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
     * @requestMediaType multipart/form-data
     */
    public function store(StoreBlogPostRequest $request): JsonResponse
    {
        $postData = $request->validated();
        $postData['author_id'] = $request->user('staff')?->id;

        $post = $this->blogService->createPost($postData);

        return ApiResponse::success(
            new BlogPostResource($post),
            'Post created successfully',
            null,
            201
        );
    }

    /**
     * Update an existing blog post.
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
     */
    public function destroy(int $id): JsonResponse
    {
        $post = BlogPost::query()->findOrFail($id);
        $this->blogService->deletePost($post);

        return ApiResponse::success(null, 'Post deleted successfully');
    }

    /**
     * List all comments for a specific blog post.
     */
    public function getComments(Request $request, string $slug): JsonResponse
    {
        $post = $this->blogService->getPostBySlug($slug);

        $filters = [
            'search' => $request->string('search'),
            'is_approved' => true, // Default to true to keep public views safe
        ];

        if ($request->has('is_approved')) {
            $status = $request->string('is_approved')->value();

            if ($status === 'all') {
                unset($filters['is_approved']);
            } else {
                $filters['is_approved'] = $request->boolean('is_approved');
            }
        }

        $comments = $this->blogService->getCommentsByPost($post->id, $filters);

        return ApiResponse::success(
            BlogCommentResource::collection($comments),
            'Comments retrieved successfully'
        );
    }

    /**
     * Post a comment on a blog post.
     */
    public function publicComment(StoreBlogCommentRequest $request, string $slug): JsonResponse
    {
        $post = $this->blogService->getPostBySlug($slug);

        $comment = $this->blogService->addComment(
            $post,
            $request->validated(),
            $request->user('customer')?->id
        );

        return ApiResponse::success(
            new BlogCommentResource($comment),
            'Comment submitted for review',
            null,
            201
        );
    }

    /**
     * List all blog categories.
     */
    public function categoriesIndex(Request $request): JsonResponse
    {
        $filters = [
            'search' => $request->string('search'),
        ];

        $categories = $this->blogService->getCategories($filters);

        return ApiResponse::success(
            BlogCategoryResource::collection($categories),
            'Categories retrieved successfully'
        );
    }

    /**
     * Create a new blog category.
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
     */
    public function categoriesUpdate(UpdateBlogCategoryRequest $request, int $id): JsonResponse
    {
        $category = BlogCategory::query()->findOrFail($id);
        $updatedCategory = $this->blogService->updateCategory($category, $request->validated());

        return ApiResponse::success(
            new BlogCategoryResource($updatedCategory),
            'Category updated successfully'
        );
    }

    /**
     * Delete a blog category.
     */
    public function categoriesDestroy(int $id): JsonResponse
    {
        $category = BlogCategory::query()->findOrFail($id);
        $this->blogService->deleteCategory($category);

        return ApiResponse::success(null, 'Category deleted successfully');
    }

    /**
     * Approve or update a comment (Moderation).
     */
    public function updateComment(Request $request, int $id): JsonResponse
    {
        $comment = BlogComment::query()->findOrFail($id);
        $updatedComment = $this->blogService->updateComment($comment, $request->only('is_approved', 'body'));

        return ApiResponse::success(
            new BlogCommentResource($updatedComment),
            'Comment updated successfully'
        );
    }

    /**
     * Delete a comment.
     */
    public function destroyComment(int $id): JsonResponse
    {
        $comment = BlogComment::query()->findOrFail($id);
        $this->blogService->deleteComment($comment);

        return ApiResponse::success(null, 'Comment deleted successfully');
    }
}
