<?php
namespace App\Core\Repositories;

use App\Models\BlogTag;
use App\Core\Repositories\Interfaces\BlogTagRepositoryInterface;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class BlogTagRepository implements BlogTagRepositoryInterface
{
    public function all(): Collection
    {
        return BlogTag::orderBy('usage_count', 'desc')->get();
    }

    public function findOrCreate(string $name): BlogTag
    {
        return BlogTag::firstOrCreate(['name' => $name], [
            'slug' => Str::slug($name),
        ]);
    }
}
