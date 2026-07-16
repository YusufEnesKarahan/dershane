<?php
namespace App\Observers;

use App\Models\BlogCategory;
use Illuminate\Support\Facades\Cache;

class BlogCategoryObserver
{
    public function saved(BlogCategory $category)
    {
        Cache::forget('blog.categories.list');
    }
}
