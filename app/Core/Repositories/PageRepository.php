<?php
namespace App\Core\Repositories;

use App\Core\Repositories\Interfaces\PageRepositoryInterface;
use App\DTOs\CMS\PageFilterDTO;
use App\Models\Page;

class PageRepository extends BaseRepository implements PageRepositoryInterface
{
    public function __construct(Page $model)
    {
        parent::__construct($model);
    }

    public function filterAndPaginate(PageFilterDTO $filters, int $perPage = 15)
    {
        $query = $this->model->newQuery()->with('parent');

        if ($filters->search) {
            $query->where('title', 'like', "%{$filters->search}%")
                  ->orWhere('slug', 'like', "%{$filters->search}%");
        }

        if ($filters->status) {
            $query->where('status', $filters->status);
        }

        return $query->orderBy('sort_order')->paginate($perPage);
    }

    public function getTree(): array
    {
        return $this->model->newQuery()
            ->whereNull('parent_id')
            ->with('children')
            ->orderBy('sort_order')
            ->get()
            ->toArray();
    }

    public function findBySlug(string $slug): ?Page
    {
        return $this->model->newQuery()->where('slug', $slug)->first();
    }

    public function restore(int $id): bool
    {
        $record = $this->model->withTrashed()->find($id);
        return $record ? $record->restore() : false;
    }

    public function forceDelete(int $id): bool
    {
        $record = $this->model->withTrashed()->find($id);
        return $record ? $record->forceDelete() : false;
    }
}
