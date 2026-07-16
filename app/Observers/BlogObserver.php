<?php
namespace App\Observers;

use App\Models\Blog;
use Illuminate\Support\Facades\Cache;

class BlogObserver
{
    public function saved(Blog $blog)
    {
        Cache::forget('blog.slug.' . $blog->slug);
        
        // Sitemap / Search Index updates stubs triggers
    }
}
