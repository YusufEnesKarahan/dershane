<?php
namespace App\Domain\Classroom\Actions;

use App\DTOs\Classroom\CreateClassroomDTO;
use App\Domain\Classroom\Services\ClassroomService;
use App\Models\Classroom;

class CreateClassroomAction
{
    public function __construct(protected ClassroomService $service) {}

    public function execute(CreateClassroomDTO $dto): Classroom
    {
        return $this->service->create($dto);
    }
}
