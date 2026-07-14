<?php
namespace App\Domain\CMS\Services;

use App\Core\Repositories\Interfaces\PageRepositoryInterface;
use App\DTOs\CMS\PageFilterDTO;

class PageService
{
    public function __construct(protected PageRepositoryInterface $repository) {}

    public function paginate(PageFilterDTO $filters, int $perPage = 15)
    {
        return $this->repository->filterAndPaginate($filters, $perPage);
    }
}
