<?php
namespace App\Domain\Blog\Actions;

use App\Models\Blog;

class ArchiveBlogAction
{
    public function execute(Blog $blog): void
    {
        $blog->update(['status' => 'Archived']);
    }
}
