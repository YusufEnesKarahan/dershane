<?php
namespace App\Core\Repositories\Interfaces;

use App\Models\BlogComment;
use Illuminate\Pagination\LengthAwarePaginator;

interface CommentRepositoryInterface
{
    public function paginate(int $perPage = 15): LengthAwarePaginator;
    public function findById(int $id): ?BlogComment;
    public function update(BlogComment $comment, array $data): BlogComment;
}
