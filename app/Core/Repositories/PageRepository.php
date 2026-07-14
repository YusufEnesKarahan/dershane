<?php
namespace App\Core\Repositories;
use App\Core\Repositories\Interfaces\PageRepositoryInterface;
use App\Models\Page;

class PageRepository extends BaseRepository implements PageRepositoryInterface
{
    public function __construct(Page $model)
    {
        parent::__construct($model);
    }
}
