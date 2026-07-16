<?php
namespace App\Domain\Blog\Actions;

use App\Models\Blog;

class RestoreBlogAction
{
    public function execute(Blog $blog): void
    {
        $blog->restore();
    }
}
