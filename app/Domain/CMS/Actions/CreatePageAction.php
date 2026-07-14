<?php
namespace App\Domain\CMS\Actions;

use App\DTOs\CMS\CreatePageDTO;
use App\Core\Repositories\Interfaces\PageRepositoryInterface;
use App\Domain\CMS\Services\PageSeoService;
use App\Domain\CMS\Services\SlugService;
use App\Models\Page;
use Illuminate\Support\Facades\Auth;

class CreatePageAction
{
    public function __construct(
        protected PageRepositoryInterface $repository,
        protected SlugService $slugService,
        protected PageSeoService $seoService
    ) {}

    public function execute(CreatePageDTO $dto): Page
    {
        $slug = $dto->slug ?: $this->slugService->generate($dto->title);
        $seo = $this->seoService->format($dto->seo);

        // Enforce homepage unique check
        if ($dto->is_homepage) {
            Page::where('is_homepage', true)->update(['is_homepage' => false]);
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
            'is_system' => $dto->is_system,
            'status' => 'draft',
            'author_id' => Auth::id(),
        ], $seo);

        return $this->repository->create($data);
    }
}
