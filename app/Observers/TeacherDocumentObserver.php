<?php
namespace App\Observers;

use App\Models\TeacherDocument;

class TeacherDocumentObserver
{
    public function saved(TeacherDocument $doc)
    {
        // Document caches invalidate
    }
}
