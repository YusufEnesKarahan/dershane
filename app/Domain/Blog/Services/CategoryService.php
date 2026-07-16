<?php
namespace App\Domain\Blog\Services;

use App\DTOs\Blog\CategoryDTO;
use App\Models\BlogCategory;
use Illuminate\Support\Str;

class CategoryService
{
    public function createCategory(CategoryDTO $dto): BlogCategory
    {
        return BlogCategory::create([
            'name' => $dto->name,
            'slug' => Str::slug($dto->name),
            'parent_id' => $dto->parent_id,
            'description' => $dto->description,
            'icon' => $dto->icon,
            'color' => $dto->color,
            'sort_order' => $dto->sort_order,
            'visibility' => $dto->visibility,
        ]);
    }
}
