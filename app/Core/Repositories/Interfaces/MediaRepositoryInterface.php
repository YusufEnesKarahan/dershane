<?php
namespace App\Core\Repositories\Interfaces;

use App\Models\Media;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

interface MediaRepositoryInterface
{
    public function paginate(array $filters, int $perPage = 24): LengthAwarePaginator;
    public function find(int $id): ?Media;
    public function findByUuid(string $uuid): ?Media;
    public function findByChecksum(string $checksum): ?Media;
    public function create(array $data): Media;
    public function update(Media $media, array $data): Media;
    public function delete(Media $media): bool;
    public function getByFolder(?int $folderId): Collection;
    public function getCollections(): Collection;
}
