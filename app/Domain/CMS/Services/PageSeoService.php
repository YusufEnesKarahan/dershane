<?php
namespace App\Domain\CMS\Services;

class PageSeoService
{
    public function format(array $seoData): array
    {
        return [
            'meta_title' => $seoData['meta_title'] ?? null,
            'meta_description' => $seoData['meta_description'] ?? null,
            'meta_keywords' => $seoData['meta_keywords'] ?? null,
            'og_title' => $seoData['og_title'] ?? null,
            'og_description' => $seoData['og_description'] ?? null,
            'og_image' => $seoData['og_image'] ?? null,
            'canonical_url' => $seoData['canonical_url'] ?? null,
            'robots' => $seoData['robots'] ?? 'index, follow',
        ];
    }
}
