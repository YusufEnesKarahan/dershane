<?php
namespace App\Core\Repositories\Interfaces;

use App\Models\Blog;
use Illuminate\Pagination\LengthAwarePaginator;

interface BlogRepositoryInterface
{
    public function paginate(int $perPage = 15, array $filters = []): LengthAwarePaginator;
    public function findById(int $id): ?Blog;
    public function findBySlug(string $slug): ?Blog;
    public function create(array $data): Blog;
    public function update(Blog $blog, array $data): Blog;
    public function delete(Blog $blog): bool;
}
