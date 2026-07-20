<?php
namespace App\Domain\Course\Services;

use App\DTOs\Course\CreateCourseDTO;
use App\DTOs\Course\UpdateCourseDTO;
use App\Core\Repositories\Interfaces\CourseRepositoryInterface;
use App\Models\Course;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class CourseService
{
    public function __construct(protected CourseRepositoryInterface $repository) {}

    public function create(CreateCourseDTO $dto): Course
    {
        // Duplicate Course Code check constraint
        $exists = $this->repository->findByCode($dto->code);
        if ($exists) {
            throw ValidationException::withMessages([
                'code' => 'Course code must be unique system-wide.',
            ]);
        }

        return $this->repository->create([
            'code' => $dto->code,
            'name' => $dto->name,
            'slug' => Str::slug($dto->name),
            'description' => $dto->description,
            'course_level_id' => $dto->course_level_id,
            'duration' => $dto->duration,
            'capacity' => $dto->capacity,
            'status' => $dto->status,
            'is_active' => $dto->is_active,
            'cover_image' => $dto->cover_image,
        ]);
    }

    public function update(Course $course, UpdateCourseDTO $dto): Course
    {
        $updated = $this->repository->update($course, [
            'name' => $dto->name,
            'description' => $dto->description,
            'course_level_id' => $dto->course_level_id,
            'duration' => $dto->duration,
            'capacity' => $dto->capacity,
            'status' => $dto->status,
            'is_active' => $dto->is_active,
            'cover_image' => $dto->cover_image,
        ]);

        $updated->teachers()->sync($dto->teachers);
        $updated->branches()->sync($dto->branches);
        $updated->prerequisites()->sync($dto->prerequisites);

        return $updated;
    }
}
