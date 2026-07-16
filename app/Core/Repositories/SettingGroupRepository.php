<?php
namespace App\Core\Repositories;

use App\Models\SettingGroup;
use App\Core\Repositories\Interfaces\SettingGroupRepositoryInterface;
use Illuminate\Support\Collection;

class SettingGroupRepository implements SettingGroupRepositoryInterface
{
    public function all(): Collection
    {
        return SettingGroup::with('settings')->orderBy('sort_order')->get();
    }

    public function findBySlug(string $slug): ?SettingGroup
    {
        return SettingGroup::where('slug', $slug)->with('settings')->first();
    }

    public function create(array $data): SettingGroup
    {
        return SettingGroup::create($data);
    }
}
