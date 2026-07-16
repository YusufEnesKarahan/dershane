<?php
namespace App\Domain\CMS\Actions;

use App\DTOs\CMS\UpdatePageDTO;
use App\Core\Repositories\Interfaces\PageRepositoryInterface;
use App\Domain\CMS\Services\PageSeoService;
use App\Domain\CMS\Services\SlugService;
use App\Models\Page;

class UpdatePageAction
{
    public function __construct(
        protected PageRepositoryInterface $repository,
        protected SlugService $slugService,
        protected PageSeoService $seoService
    ) {}

    public function execute(Page $page, UpdatePageDTO $dto): Page
    {
        $slug = $dto->slug ?: $this->slugService->generate($dto->title, $page->id);
        
        if (!$this->slugService->validateUnique($slug, $page->id)) {
            abort(422, 'Slug is reserved or already in use.');
        }

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
