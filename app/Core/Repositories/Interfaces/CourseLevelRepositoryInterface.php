<?php
namespace App\Core\Repositories\Interfaces;

use App\Models\CourseLevel;
use Illuminate\Support\Collection;

interface CourseLevelRepositoryInterface
{
    public function all(): Collection;
    public function create(array $data): CourseLevel;
}
