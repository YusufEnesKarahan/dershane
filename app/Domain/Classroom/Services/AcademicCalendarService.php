<?php
namespace App\Domain\Classroom\Services;

use App\Models\AcademicTerm;

class AcademicCalendarService
{
    public function createTerm(array $data): AcademicTerm
    {
        return AcademicTerm::create($data);
    }
}
