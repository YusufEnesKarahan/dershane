<?php
namespace App\Domain\Blog\Services;

use App\DTOs\Blog\CommentDTO;
use App\Models\BlogComment;
use Illuminate\Support\Facades\Auth;

class CommentService
{
    public function createComment(CommentDTO $dto): BlogComment
    {
        return BlogComment::create([
            'blog_id' => $dto->blog_id,
            'parent_id' => $dto->parent_id,
            'user_id' => Auth::id(),
            'author_name' => $dto->author_name ?: (Auth::user() ? Auth::user()->name : 'Anonim'),
            'author_email' => $dto->author_email ?: (Auth::user() ? Auth::user()->email : null),
            'content' => $dto->content,
            'status' => Auth::check() ? 'Approved' : 'Pending',
        ]);
    }
}
