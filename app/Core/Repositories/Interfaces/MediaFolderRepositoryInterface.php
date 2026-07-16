<?php
namespace App\Core\Repositories\Interfaces;

use App\Models\MediaFolder;
use Illuminate\Support\Collection;

interface MediaFolderRepositoryInterface
{
    public function all(): Collection;
    public function find(int $id): ?MediaFolder;
    public function findBySlug(string $slug): ?MediaFolder;
    public function create(array $data): MediaFolder;
    public function update(MediaFolder $folder, array $data): MediaFolder;
    public function delete(MediaFolder $folder): bool;
    public function getTree(): Collection;
}
