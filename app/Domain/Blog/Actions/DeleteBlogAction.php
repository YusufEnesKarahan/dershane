<?php
namespace App\Domain\Blog\Actions;

use App\Models\Blog;

class DeleteBlogAction
{
    public function execute(Blog $blog): void
    {
        $blog->delete();
    }
}
