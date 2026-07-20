<?php
namespace App\Domain\Student\Services;

use App\Models\StudentDocument;

class StudentDocumentService
{
    public function addDocument(int $studentId, string $title, string $type, string $filePath): StudentDocument
    {
        return StudentDocument::create([
            'student_id' => $studentId,
            'title' => $title,
            'type' => $type,
            'file_path' => $filePath,
        ]);
    }
}
