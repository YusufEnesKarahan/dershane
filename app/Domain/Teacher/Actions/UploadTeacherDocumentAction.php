<?php
namespace App\Domain\Teacher\Actions;

use App\Models\Teacher;
use App\Domain\Teacher\Services\TeacherDocumentService;
use App\Models\TeacherDocument;

class UploadTeacherDocumentAction
{
    public function __construct(protected TeacherDocumentService $service) {}

    public function execute(Teacher $teacher, array $data): TeacherDocument
    {
        return $this->service->uploadDocument($teacher, $data);
    }
}
