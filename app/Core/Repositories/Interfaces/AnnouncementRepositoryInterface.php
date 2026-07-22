<?php

namespace App\Core\Repositories\Interfaces;

use App\Models\Announcement;
use Illuminate\Pagination\LengthAwarePaginator;

interface AnnouncementRepositoryInterface
{
    public function paginate(int $perPage = 15, array $filters = []): LengthAwarePaginator;
    public function create(array $data): Announcement;
}
