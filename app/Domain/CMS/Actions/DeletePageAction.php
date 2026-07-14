<?php
namespace App\Domain\CMS\Actions;

use App\Core\Repositories\Interfaces\PageRepositoryInterface;
use App\Models\Page;

class DeletePageAction
{
    public function __construct(protected PageRepositoryInterface $repository) {}

    public function execute(Page $page): bool
    {
        if ($page->isSystemPage()) {
            abort(403, 'System pages cannot be deleted.');
        }

        if ($page->isHomepage()) {
            abort(403, 'Homepage cannot be deleted.');
        }

        return $this->repository->delete($page->id);
    }
}
