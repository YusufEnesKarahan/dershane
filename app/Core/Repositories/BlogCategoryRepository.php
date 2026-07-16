<?php
namespace App\Core\Repositories;

use App\Models\BlogCategory;
use App\Core\Repositories\Interfaces\BlogCategoryRepositoryInterface;
use Illuminate\Support\Collection;

class BlogCategoryRepository implements BlogCategoryRepositoryInterface
{
    public function all(): Collection
    {
        return BlogCategory::with('children')->whereNull('parent_id')->orderBy('sort_order')->get();
    }

    public function create(array $data): BlogCategory
    {
        return BlogCategory::create($data);
    }
}
