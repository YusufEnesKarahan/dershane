<?php
namespace App\Domain\Teacher\Services;

class TeacherExportService
{
    public function exportToCsv(array $teacherIds): string
    {
        return "name,email,branch\n";
    }
}
