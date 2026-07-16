<?php
namespace App\Domain\Blog\Services;

use App\Models\BlogTag;

class TagService
{
    public function mergeTags(BlogTag $sourceTag, BlogTag $targetTag): void
    {
        foreach ($sourceTag->blogs as $blog) {
            if (!$targetTag->blogs()->where('blog_id', $blog->id)->exists()) {
                $targetTag->blogs()->attach($blog->id);
            }
        }
        $sourceTag->blogs()->detach();
        
        $targetTag->increment('usage_count', $sourceTag->usage_count);
        $sourceTag->delete();
    }
}
