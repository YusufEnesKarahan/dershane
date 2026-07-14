<?php
namespace App\Domain\CMS\Services;

use Illuminate\Support\Str;
use App\Core\Repositories\Interfaces\PageRepositoryInterface;

class SlugService
{
    public function __construct(protected PageRepositoryInterface $repository) {}

    public function generate(string $title, ?int $ignoreId = null): string
    {
        $slug = Str::slug($title);
        $originalSlug = $slug;
        $count = 1;

        while ($this->exists($slug, $ignoreId)) {
            $slug = "{$originalSlug}-{$count}";
            $count++;
        }

        return $slug;
    }

    protected function exists(string $slug, ?int $ignoreId = null): bool
    {
        $page = $this->repository->findBySlug($slug);
        if (!$page) {
            return false;
        }
        return $ignoreId === null ? true : $page->id !== $ignoreId;
    }
}
