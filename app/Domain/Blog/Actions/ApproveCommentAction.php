<?php
namespace App\Domain\Blog\Actions;

use App\Models\BlogComment;
use App\Domain\Blog\Services\CommentModerationService;

class ApproveCommentAction
{
    public function __construct(protected CommentModerationService $service) {}

    public function execute(BlogComment $comment): void
    {
        $this->service->approve($comment);
    }
}
