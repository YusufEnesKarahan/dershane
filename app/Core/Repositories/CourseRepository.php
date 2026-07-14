<?php
namespace App\Core\Repositories;
use App\Core\Repositories\Interfaces\CourseRepositoryInterface;
use App\Models\Course;

class CourseRepository extends BaseRepository implements CourseRepositoryInterface
{
    public function __construct(Course $model)
    {
        parent::__construct($model);
    }
}
