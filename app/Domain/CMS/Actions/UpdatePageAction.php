<?php
namespace App\Domain\CMS\Actions;

use App\DTOs\CMS\UpdatePageDTO;
use App\Core\Repositories\Interfaces\PageRepositoryInterface;
use App\Domain\CMS\Services\PageSeoService;
use App\Domain\CMS\Services\SlugService;
use App\Domain\CMS\Services\RevisionService;
use App\Models\Page;

class UpdatePageAction
{
    public function __construct(
        protected PageRepositoryInterface $repository,
        protected SlugService $slugService,
        protected PageSeoService $seoService,
        protected RevisionService $revisionService
    ) {}

    public function execute(Page $page, UpdatePageDTO $dto): Page
    {
        // 1. Snapshot old version for revision
        $this->revisionService->createSnapshot($page);

        // 2. Slug checking
        $slug = $dto->slug ?: $this->slugService->generate($dto->title, $page->id);
        $seo = $this->seoService->format($dto->seo);

        if ($dto->is_homepage) {
            Page::where('is_homepage', true)->where('id', '!=', $page->id)->update(['is_homepage' => false]);
        }

        $data = array_merge([
            'title' => $dto->title,
            'slug' => $slug,
            'content' => $dto->content,
            'excerpt' => $dto->excerpt,
            'template' => $dto->template,
            'parent_id' => $dto->parent_id,
            'sort_order' => $dto->sort_order,
            'is_homepage' => $dto->is_homepage,
        ], $seo);

        $page->update($data);

        return $page;
    }
}
