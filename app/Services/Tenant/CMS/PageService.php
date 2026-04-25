<?php

namespace App\Services\Tenant\CMS;

use App\Models\Tenant\CMS\Page;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

/**
 * Class PageService
 * * Handles business logic related to tenant CMS pages.
 */
class PageService
{
    /**
     * Retrieve a paginated, filtered list of pages.
     *
     * @param array $filters Query filters (e.g., search, is_published)
     * @param int $perPage Items per page
     * @return LengthAwarePaginator
     */
    public function getPaginatedPages(array $filters = [], int $perPage = 20): LengthAwarePaginator
    {
        return Page::query()
            ->when($filters['search'] ?? null, fn($q, $v) => $q->where('title', 'like', "%{$v}%"))
            ->when(isset($filters['is_published']), fn($q) => $q->where('is_published', (bool)$filters['is_published']))
            ->latest('id')
            ->paginate($perPage);
    }

    /**
     * Retrieve a specific published page by its slug.
     *
     * @param string $slug
     * @return Page
     */
    public function getPageBySlug(string $slug): Page
    {
        return Page::query()
            ->where('is_published', true)
            ->where('slug', $slug)
            ->firstOrFail();
    }

    /**
     * Create a new page.
     *
     * @param array $data Validated page data.
     * @return Page
     */
    public function createPage(array $data): Page
    {
        return Page::query()->create($data);
    }

    /**
     * Update an existing page.
     *
     * @param Page $page
     * @param array $data Validated update data.
     * @return Page
     */
    public function updatePage(Page $page, array $data): Page
    {
        $page->update($data);
        return $page->fresh();
    }

    /**
     * Delete a page.
     *
     * @param Page $page
     * @return void
     */
    public function deletePage(Page $page): void
    {
        $page->delete();
    }

    /**
     * Force publish a page.
     *
     * @param Page $page
     * @return Page
     */
    public function publishPage(Page $page): Page
    {
        $page->update([
            'is_published' => true,
            'published_at' => $page->published_at ?? now()
        ]);

        return $page->refresh();
    }
}
