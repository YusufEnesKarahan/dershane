<?php
namespace App\Domain\Blog\Services;

use App\Models\Blog;

class RelatedPostService
{
    public function syncRelated(Blog $blog, array $relatedIds): void
    {
        $blog->relatedPosts()->sync($relatedIds);
    }
}
