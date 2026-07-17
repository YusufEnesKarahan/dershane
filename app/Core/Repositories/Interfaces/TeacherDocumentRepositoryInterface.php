<?php
namespace App\Core\Repositories\Interfaces;

use App\Models\TeacherDocument;
use Illuminate\Support\Collection;

interface TeacherDocumentRepositoryInterface
{
    public function getByTeacher(int $teacherId): Collection;
    public function create(array $data): TeacherDocument;
}
