<?php
namespace App\Domain\Teacher\Actions;

use App\Models\Teacher;

class RestoreTeacherAction
{
    public function execute(Teacher $teacher): void
    {
        $teacher->restore();
    }
}
