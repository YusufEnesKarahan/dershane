<?php
namespace App\Core\Repositories\Interfaces;

use App\Models\BlogTag;
use Illuminate\Support\Collection;

interface BlogTagRepositoryInterface
{
    public function all(): Collection;
    public function findOrCreate(string $name): BlogTag;
}
