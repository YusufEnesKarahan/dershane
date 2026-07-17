<?php
namespace App\Domain\Teacher\Actions;

use App\Models\Teacher;

class DeleteTeacherAction
{
    public function execute(Teacher $teacher): void
    {
        $teacher->delete();
    }
}
