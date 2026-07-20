<?php
namespace App\Core\Repositories;

use App\Models\CourseLevel;
use App\Core\Repositories\Interfaces\CourseLevelRepositoryInterface;
use Illuminate\Support\Collection;

class CourseLevelRepository implements CourseLevelRepositoryInterface
{
    public function all(): Collection
    {
        return CourseLevel::orderBy('name')->get();
    }

    public function create(array $data): CourseLevel
    {
        return CourseLevel::create($data);
    }
}
