<?php
namespace App\Domain\CMS\Services;

use App\Models\Page;

class SeoAnalyzerService
{
    public function analyze(Page $page): array
    {
        $score = 100;
        $issues = [];

        // Meta Title Check
        $titleLen = strlen($page->meta_title ?? '');
        if ($titleLen === 0) {
            $score -= 20;
            $issues[] = 'Meta Title boş bırakılamaz.';
        } elseif ($titleLen > 60) {
            $score -= 10;
            $issues[] = 'Meta Title çok uzun (Maks 60 karakter önerilir).';
        }

        // Meta Description Check
        $descLen = strlen($page->meta_description ?? '');
        if ($descLen === 0) {
            $score -= 20;
            $issues[] = 'Meta Description boş bırakılamaz.';
        } elseif ($descLen > 160) {
            $score -= 10;
            $issues[] = 'Meta Description çok uzun (Maks 160 karakter önerilir).';
        }

        // H1 Tag Check
        $h1Count = substr_count(strtolower($page->content ?? ''), '# ');
        if ($h1Count === 0) {
            $score -= 10;
            $issues[] = 'Sayfa içeriğinde en az bir adet H1 başlığı bulunmalıdır (# başlık).';
        } elseif ($h1Count > 1) {
            $score -= 5;
            $issues[] = 'Sayfa içeriğinde birden fazla H1 başlığı bulunmamalıdır.';
        }

        // Keywords Check
        if (empty($page->meta_keywords)) {
            $score -= 10;
            $issues[] = 'Meta Keywords tanımlanmamış.';
        }

        // Robots & Canonical Check
        if (empty($page->robots)) {
            $score -= 5;
            $issues[] = 'Robots etiketi eksik.';
        }
        if (empty($page->canonical_url)) {
            $score -= 5;
            $issues[] = 'Canonical URL eksik.';
        }

        return [
            'score' => max(0, $score),
            'issues' => $issues,
        ];
    }
}
