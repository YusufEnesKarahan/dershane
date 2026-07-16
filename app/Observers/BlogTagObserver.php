<?php
namespace App\Observers;

use App\Models\BlogTag;

class BlogTagObserver
{
    public function saved(BlogTag $tag)
    {
        // Tag caches invalidate
    }
}
