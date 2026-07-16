<?php
namespace App\Domain\Blog\Services;

use App\Models\Blog;

class BlogSeoService
{
    public function generateSchemaMarkup(Blog $blog): string
    {
        $markup = [
            '@context' => 'https://schema.org',
            '@type' => 'Article',
            'headline' => $blog->title,
            'description' => $blog->seo_description ?: $blog->excerpt,
            'datePublished' => $blog->published_at ? $blog->published_at->toIso8601String() : $blog->created_at->toIso8601String(),
            'author' => [
                '@type' => 'Person',
                'name' => $blog->author ? $blog->author->name : 'Dershane',
            ]
        ];

        return '<script type="application/ld+json">' . json_encode($markup, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT) . '</script>';
    }
}
