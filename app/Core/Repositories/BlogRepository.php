<?php
namespace App\Core\Repositories;
use App\Core\Repositories\Interfaces\BlogRepositoryInterface;
use App\Models\Blog;

class BlogRepository extends BaseRepository implements BlogRepositoryInterface
{
    public function __construct(Blog $model)
    {
        parent::__construct($model);
    }
}
