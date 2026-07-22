<?php

namespace App\Core\Repositories;

use App\Models\Announcement;
use App\Core\Repositories\Interfaces\AnnouncementRepositoryInterface;
use Illuminate\Pagination\LengthAwarePaginator;

class AnnouncementRepository implements AnnouncementRepositoryInterface
{
    public function paginate(int $perPage = 15, array $filters = []): LengthAwarePaginator
    {
        $query = Announcement::with(['group'])->withCount('reads')->orderBy('published_at', 'desc');

        if (!empty($filters['search'])) {
            $query->where('title', 'like', "%{$filters['search']}%");
        }

        return $query->paginate($perPage);
    }

    public function create(array $data): Announcement
    {
        return Announcement::create($data);
    }
}
