<?php
namespace App\Domain\Blog\Services;

use App\Models\BlogComment;

class CommentModerationService
{
    public function approve(BlogComment $comment): void
    {
        $comment->update(['status' => 'Approved']);
    }

    public function reject(BlogComment $comment): void
    {
        $comment->update(['status' => 'Rejected']);
    }

    public function markAsSpam(BlogComment $comment): void
    {
        $comment->update(['status' => 'Spam']);
    }
}
