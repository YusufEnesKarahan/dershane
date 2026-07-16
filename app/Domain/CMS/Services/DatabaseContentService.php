<?php
namespace App\Domain\CMS\Services;

use App\Core\Services\DemoContentService;
use App\Models\Page;
use Illuminate\Support\Facades\Cache;

class DatabaseContentService
{
    public function __construct(protected DemoContentService $demoContentService) {}

    public function getContentForTemplate(string $template): array
    {
        $cacheKey = 'cms.page.template.' . $template;
        $cacheTime = config('cms.cache_time', 3600);

        return Cache::remember($cacheKey, $cacheTime, function () use ($template) {
            $page = Page::where('template', $template)->where('status', \App\Enums\PageStatus::Published)->first();
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
        });
    }

    public function clearCache(Page $page): void
    {
        if ($page->template) {
            Cache::forget('cms.page.template.' . $page->template);
        }
        Cache::forget('cms.page.slug.' . $page->slug);
    }
}
