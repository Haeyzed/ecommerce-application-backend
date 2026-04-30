<?php

namespace App\Http\Controllers\Tenant\Api\CMS;

use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\Tenant\CMS\StorePageRequest;
use App\Http\Requests\Tenant\CMS\UpdatePageRequest;
use App\Http\Resources\Tenant\CMS\PageResource;
use App\Models\Tenant\CMS\Page;
use App\Services\Tenant\CMS\PageService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Page Endpoints
 * * Handles management of CMS pages.
 */
class PageController extends Controller
{
    public function __construct(
        private readonly PageService $pageService
    ) {}

    /**
     * List all pages (Admin/Staff view).
     */
    public function index(Request $request): JsonResponse
    {
        $perPage = $request->integer('per_page', 20);

        $filters = [
            'search' => $request->string('search'),
        ];

        if ($request->has('is_published')) {
            $filters['is_published'] = $request->boolean('is_published');
        }

        $pages = $this->pageService->getPaginatedPages($filters, $perPage);

        return ApiResponse::success(
            data: PageResource::collection($pages),
            message: 'Pages retrieved successfully',
            meta: ApiResponse::meta($pages)
        );
    }

    /**
     * Show a specific page (Staff access via ID).
     */
    public function show(int $id): JsonResponse
    {
        $page = Page::query()->findOrFail($id);

        return ApiResponse::success(
            new PageResource($page),
            'Page retrieved successfully'
        );
    }

    /**
     * Show a specific published page by slug (Public access).
     */
    public function showPublic(string $slug): JsonResponse
    {
        $page = $this->pageService->getPageBySlug($slug);

        return ApiResponse::success(
            new PageResource($page),
            'Page retrieved successfully'
        );
    }

    /**
     * Create a new page.
     */
    public function store(StorePageRequest $request): JsonResponse
    {
        $page = $this->pageService->createPage($request->validated());

        return ApiResponse::success(
            new PageResource($page),
            'Page created successfully',
            null,
            201
        );
    }

    /**
     * Update an existing page.
     */
    public function update(UpdatePageRequest $request, int $id): JsonResponse
    {
        $page = Page::query()->findOrFail($id);
        $updatedPage = $this->pageService->updatePage($page, $request->validated());

        return ApiResponse::success(
            new PageResource($updatedPage),
            'Page updated successfully'
        );
    }

    /**
     * Delete a page.
     */
    public function destroy(int $id): JsonResponse
    {
        $page = Page::query()->findOrFail($id);
        $this->pageService->deletePage($page);

        return ApiResponse::success(null, 'Page deleted successfully');
    }

    /**
     * Force publish a page.
     */
    public function publish(int $id): JsonResponse
    {
        $page = Page::query()->findOrFail($id);
        $publishedPage = $this->pageService->publishPage($page);

        return ApiResponse::success(
            new PageResource($publishedPage),
            'Page published successfully'
        );
    }
}
