<?php
namespace App\Core\Repositories\Interfaces;

use App\Models\BlogCategory;
use Illuminate\Support\Collection;

interface BlogCategoryRepositoryInterface
{
    public function all(): Collection;
    public function create(array $data): BlogCategory;
}
