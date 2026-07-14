<?php
namespace App\Domain\CMS\Services;

use App\Core\Services\DemoContentService;
use App\Models\Page;

class DatabaseContentService
{
    public function __construct(protected DemoContentService $demoContentService) {}

    public function getContentForTemplate(string $template): array
    {
        $page = Page::where('template', $template)->where('status', 'published')->first();
        if ($page) {
            return [
                'title' => $page->title,
                'content' => $page->content,
                'excerpt' => $page->excerpt,
                'seo' => [
                    'meta_title' => $page->meta_title,
                    'meta_description' => $page->meta_description,
                ],
            ];
        }

        // Fallback to DemoContentService
        return match ($template) {
            'hero' => $this->demoContentService->getHeroContent(),
            'about' => $this->demoContentService->getAbout(),
            'faq' => $this->demoContentService->getFaq(),
            default => [],
        };
    }
}
