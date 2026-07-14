<?php
namespace App\Core\Repositories;
use App\Core\Repositories\Interfaces\TeacherRepositoryInterface;
use App\Models\Teacher;

class TeacherRepository extends BaseRepository implements TeacherRepositoryInterface
{
    public function __construct(Teacher $model)
    {
        parent::__construct($model);
    }
}
