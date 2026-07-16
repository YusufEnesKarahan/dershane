<?php
namespace App\Core\Repositories;

use App\Models\Blog;
use App\Core\Repositories\Interfaces\BlogRepositoryInterface;
use Illuminate\Pagination\LengthAwarePaginator;

class BlogRepository implements BlogRepositoryInterface
{
    public function paginate(int $perPage = 15, array $filters = []): LengthAwarePaginator
    {
        $query = Blog::with(['category', 'author'])->orderBy('created_at', 'desc');

        if (!empty($filters['search'])) {
            $query->where('title', 'like', "%{$filters['search']}%");
        }

        if (!empty($filters['category_id'])) {
            $query->where('category_id', $filters['category_id']);
        }

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        return $query->paginate($perPage);
    }

    public function findById(int $id): ?Blog
    {
        return Blog::with(['category', 'author', 'tags'])->find($id);
    }

    public function findBySlug(string $slug): ?Blog
    {
        return Blog::where('slug', $slug)->first();
    }

    public function create(array $data): Blog
    {
        return Blog::create($data);
    }

    public function update(Blog $blog, array $data): Blog
    {
        $blog->update($data);
        return $blog;
    }

    public function delete(Blog $blog): bool
    {
        return $blog->delete();
    }
}
