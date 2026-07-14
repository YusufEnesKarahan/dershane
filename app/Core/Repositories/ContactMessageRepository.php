<?php
namespace App\Core\Repositories;
use App\Core\Repositories\Interfaces\ContactMessageRepositoryInterface;
use App\Models\ContactMessage;

class ContactMessageRepository extends BaseRepository implements ContactMessageRepositoryInterface
{
    public function __construct(ContactMessage $model)
    {
        parent::__construct($model);
    }
}
