<?php
namespace App\Domain\CMS\Services;

use App\Models\Page;

class SitemapService
{
    public function getUrls(): array
    {
        $pages = Page::where('status', \App\Enums\PageStatus::Published)->get();
        $urls = [];

        foreach ($pages as $page) {
            $urls[] = [
                'loc' => url($page->slug),
                'lastmod' => $page->updated_at->toAtomString(),
                'changefreq' => 'weekly',
                'priority' => $page->is_homepage ? '1.0' : '0.8',
            ];
        }

        return $urls;
    }

    public function regenerate(): void
    {
        // Dynamic sitemap compilation trigger
    }
}
