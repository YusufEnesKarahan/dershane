<?php
namespace App\Core\Repositories;
use App\Core\Repositories\Interfaces\LeadRepositoryInterface;
use App\Models\Lead;

class LeadRepository extends BaseRepository implements LeadRepositoryInterface
{
    public function __construct(Lead $model)
    {
        parent::__construct($model);
    }
}
