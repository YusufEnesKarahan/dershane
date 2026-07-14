<?php
namespace App\Core\Repositories\Interfaces;

use App\DTOs\CMS\PageFilterDTO;
use App\Models\Page;

interface PageRepositoryInterface extends BaseRepositoryInterface
{
    public function filterAndPaginate(PageFilterDTO $filters, int $perPage = 15);
    public function getTree(): array;
    public function findBySlug(string $slug): ?Page;
    public function restore(int $id): bool;
    public function forceDelete(int $id): bool;
}
