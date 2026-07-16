<?php
namespace App\Core\Repositories;

use App\Models\BlogComment;
use App\Core\Repositories\Interfaces\CommentRepositoryInterface;
use Illuminate\Pagination\LengthAwarePaginator;

class CommentRepository implements CommentRepositoryInterface
{
    public function paginate(int $perPage = 15): LengthAwarePaginator
    {
        return BlogComment::with('blog')->orderBy('created_at', 'desc')->paginate($perPage);
    }

    public function findById(int $id): ?BlogComment
    {
        return BlogComment::find($id);
    }

    public function update(BlogComment $comment, array $data): BlogComment
    {
        $comment->update($data);
        return $comment;
    }
}
