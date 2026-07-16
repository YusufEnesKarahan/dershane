<?php
namespace App\Observers;

use App\Models\BlogComment;

class BlogCommentObserver
{
    public function saved(BlogComment $comment)
    {
        // Comment count refresh trigger logic stubs
    }
}
