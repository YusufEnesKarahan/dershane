<?php
namespace App\Core\Repositories;

use App\Models\MediaFolder;
use App\Core\Repositories\Interfaces\MediaFolderRepositoryInterface;
use Illuminate\Support\Collection;

class MediaFolderRepository implements MediaFolderRepositoryInterface
{
    public function all(): Collection
    {
        return MediaFolder::orderBy('order_column')->get();
    }

    public function find(int $id): ?MediaFolder
    {
        return MediaFolder::find($id);
    }

    public function findBySlug(string $slug): ?MediaFolder
    {
        return MediaFolder::where('slug', $slug)->first();
    }

    public function create(array $data): MediaFolder
    {
        return MediaFolder::create($data);
    }

    public function update(MediaFolder $folder, array $data): MediaFolder
    {
        $folder->update($data);
        return $folder;
    }

    public function delete(MediaFolder $folder): bool
    {
        return $folder->delete();
    }

    public function getTree(): Collection
    {
        return MediaFolder::whereNull('parent_id')->with('children')->orderBy('order_column')->get();
    }
}
