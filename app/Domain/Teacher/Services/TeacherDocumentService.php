<?php
namespace App\Domain\Teacher\Services;

use App\Models\Teacher;
use App\Models\TeacherDocument;

class TeacherDocumentService
{
    public function uploadDocument(Teacher $teacher, array $data): TeacherDocument
    {
        return TeacherDocument::create([
            'teacher_id' => $teacher->id,
            'title' => $data['title'],
            'type' => $data['type'] ?? 'Identity',
            'file_path' => $data['file_path'],
        ]);
    }
}
