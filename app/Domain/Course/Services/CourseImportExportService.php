<?php
namespace App\Domain\Course\Services;

class CourseImportExportService
{
    public function importFromCsv(string $filePath): void
    {
        // CSV import stubs
    }

    public function exportToCsv(array $courseIds): string
    {
        return "code,name,price\n";
    }
}
