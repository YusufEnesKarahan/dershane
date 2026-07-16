<?php
namespace App\Core\Repositories\Interfaces;

use App\Models\SettingGroup;
use Illuminate\Support\Collection;

interface SettingGroupRepositoryInterface
{
    public function all(): Collection;
    public function findBySlug(string $slug): ?SettingGroup;
    public function create(array $data): SettingGroup;
}
