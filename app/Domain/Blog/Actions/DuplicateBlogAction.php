<?php
namespace App\Domain\Blog\Actions;

use App\Models\Blog;

class DuplicateBlogAction
{
    public function execute(Blog $blog): Blog
    {
        $clone = $blog->replicate();
        $clone->title = $clone->title . ' (Kopya)';
        $clone->slug = $clone->slug . '-kopya';
        $clone->status = 'Draft';
        $clone->save();

        return $clone;
    }
}
