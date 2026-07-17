<?php
namespace App\Domain\Teacher\Actions;

use App\Models\Teacher;

class GenerateTeacherReportAction
{
    public function execute(Teacher $teacher): array
    {
        return [
            'teacher' => $teacher->user->name,
            'report_date' => now()->toDateString(),
        ];
    }
}
