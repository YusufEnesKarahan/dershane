<?php
namespace App\Domain\Blog\Actions;

use App\DTOs\Blog\CreateBlogDTO;
use App\Domain\Blog\Services\BlogService;
use App\Models\Blog;

class CreateBlogAction
{
    public function __construct(protected BlogService $service) {}

    public function execute(CreateBlogDTO $dto): Blog
    {
        return $this->service->create($dto);
    }
}
