<?php
namespace App\Domain\Blog\Actions;

use App\DTOs\Blog\UpdateBlogDTO;
use App\Domain\Blog\Services\BlogService;
use App\Models\Blog;

class UpdateBlogAction
{
    public function __construct(protected BlogService $service) {}

    public function execute(Blog $blog, UpdateBlogDTO $dto): Blog
    {
        return $this->service->update($blog, $dto);
    }
}
