<?php
namespace App\Observers;

use App\Models\Page;
use App\Domain\CMS\Services\DatabaseContentService;
use App\Domain\CMS\Services\SitemapService;
use App\Domain\CMS\Services\RevisionService;
use App\Core\Contracts\ActivityLoggerInterface;
use App\Enums\PageStatus;

class PageObserver
{
    public function __construct(
        protected DatabaseContentService $contentService,
        protected SitemapService $sitemapService,
        protected RevisionService $revisionService,
        protected ActivityLoggerInterface $logger
    ) {}

    public function updating(Page $page)
    {
        // Snapshot old page attributes for revision before saving
        $original = Page::find($page->id);
        if ($original) {
            $this->revisionService->createSnapshot($original);
        }
    }

    public function updated(Page $page)
    {
        $this->contentService->clearCache($page);
        $this->sitemapService->regenerate();

        $this->logger->log('Page Updated', ['page_id' => $page->id, 'title' => $page->title]);

        // Dispatch specific event statuses
        if ($page->isDirty('status')) {
            if ($page->status === PageStatus::Published) {
                event(new \App\Events\CMS\PagePublished($page));
            } elseif ($page->status === PageStatus::Archived) {
                event(new \App\Events\CMS\PageArchived($page));
            }
        }

        event(new \App\Events\CMS\PageUpdated($page));
    }

    public function deleted(Page $page)
    {
        $this->contentService->clearCache($page);
        $this->sitemapService->regenerate();

        $this->logger->log('Page Deleted', ['page_id' => $page->id, 'title' => $page->title]);
        event(new \App\Events\CMS\PageDeleted($page));
    }

    public function restored(Page $page)
    {
        $this->contentService->clearCache($page);
        $this->sitemapService->regenerate();

        event(new \App\Events\CMS\PageRestored($page));
    }
}
