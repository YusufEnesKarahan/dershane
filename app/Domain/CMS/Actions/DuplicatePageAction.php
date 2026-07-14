<?php
namespace App\Domain\CMS\Actions;

use App\Models\Page;
use App\Domain\CMS\Services\SlugService;
use Illuminate\Support\Facades\Auth;

class DuplicatePageAction
{
    public function __construct(protected SlugService $slugService) {}

    public function execute(Page $page): Page
    {
        $newTitle = $page->title . ' (Copy)';
        $newSlug = $this->slugService->generate($newTitle);

        return Page::create([
            'title' => $newTitle,
            'slug' => $newSlug,
            'content' => $page->content,
            'excerpt' => $page->excerpt,
            'meta_title' => $page->meta_title,
            'meta_description' => $page->meta_description,
            'meta_keywords' => $page->meta_keywords,
            'og_title' => $page->og_title,
            'og_description' => $page->og_description,
            'og_image' => $page->og_image,
            'canonical_url' => $page->canonical_url,
            'robots' => $page->robots,
            'template' => $page->template,
            'status' => 'draft',
            'parent_id' => $page->parent_id,
            'sort_order' => $page->sort_order + 1,
            'author_id' => Auth::id(),
        ]);
    }
}
