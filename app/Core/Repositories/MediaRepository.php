<?php
namespace App\Core\Repositories;

use App\Models\Media;
use App\Core\Repositories\Interfaces\MediaRepositoryInterface;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class MediaRepository implements MediaRepositoryInterface
{
    public function paginate(array $filters, int $perPage = 24): LengthAwarePaginator
    {
        $query = Media::query();

        if (!empty($filters['folder_id'])) {
            $query->where('folder_id', $filters['folder_id']);
        } elseif (isset($filters['folder_id']) && $filters['folder_id'] === null) {
            $query->whereNull('folder_id');
        }

        if (!empty($filters['collection'])) {
            $query->where('collection', $filters['collection']);
        }

        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function($q) use ($search) {
                $q->where('filename', 'like', "%{$search}%")
                  ->orWhere('alt', 'like', "%{$search}%")
                  ->orWhere('caption', 'like', "%{$search}%")
                  ->orWhere('title', 'like', "%{$search}%");
            });
        }

        if (!empty($filters['type'])) {
            $type = $filters['type'];
            if ($type === 'image') {
                $query->where('mime_type', 'like', 'image/%');
            } elseif ($type === 'pdf') {
                $query->where('mime_type', 'application/pdf');
            } elseif ($type === 'video') {
                $query->where('mime_type', 'like', 'video/%');
            } elseif ($type === 'audio') {
                $query->where('mime_type', 'like', 'audio/%');
            }
        }

        return $query->orderBy('created_at', 'desc')->paginate($perPage);
    }

    public function find(int $id): ?Media
    {
        return Media::find($id);
    }

    public function findByUuid(string $uuid): ?Media
    {
        return Media::where('uuid', $uuid)->first();
    }

    public function findByChecksum(string $checksum): ?Media
    {
        return Media::where('checksum', $checksum)->first();
    }

    public function create(array $data): Media
    {
        return Media::create($data);
    }

    public function update(Media $media, array $data): Media
    {
        $media->update($data);
        return $media;
    }

    public function delete(Media $media): bool
    {
        return $media->delete();
    }

    public function getByFolder(?int $folderId): Collection
    {
        return Media::where('folder_id', $folderId)->orderBy('created_at', 'desc')->get();
    }

    public function getCollections(): Collection
    {
        return Media::select('collection')->distinct()->pluck('collection');
    }
}
