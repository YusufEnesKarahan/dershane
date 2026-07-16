<?php
namespace App\Domain\Blog\Actions;

use App\Models\Blog;

class PublishBlogAction
{
    public function execute(Blog $blog): void
    {
        $blog->update([
            'status' => 'Published',
            'published_at' => now(),
        ]);
    }
}
