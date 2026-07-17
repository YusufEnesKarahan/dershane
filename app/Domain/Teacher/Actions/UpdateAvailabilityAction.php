<?php
namespace App\Domain\Teacher\Actions;

use App\Models\Teacher;
use App\Domain\Teacher\Services\TeacherAvailabilityService;

class UpdateAvailabilityAction
{
    public function __construct(protected TeacherAvailabilityService $service) {}

    public function execute(Teacher $teacher, array $availabilities): void
    {
        $this->service->saveAvailability($teacher, $availabilities);
    }
}
