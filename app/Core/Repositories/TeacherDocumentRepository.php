<?php
namespace App\Core\Repositories;

use App\Models\TeacherDocument;
use App\Core\Repositories\Interfaces\TeacherDocumentRepositoryInterface;
use Illuminate\Support\Collection;

class TeacherDocumentRepository implements TeacherDocumentRepositoryInterface
{
    public function getByTeacher(int $teacherId): Collection
    {
        return TeacherDocument::where('teacher_id', $teacherId)->get();
    }

    public function create(array $data): TeacherDocument
    {
        return TeacherDocument::create($data);
    }
}
