<?php
namespace App\Core\Repositories\Interfaces;

use App\Models\Attendance;
use Illuminate\Support\Collection;

interface AttendanceRepositoryInterface
{
    public function getBySession(int $sessionId): Collection;
    public function recordBulk(int $sessionId, array $records): void;
}
