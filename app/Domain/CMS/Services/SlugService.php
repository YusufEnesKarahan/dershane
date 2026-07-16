<?php
namespace App\Domain\CMS\Services;

use Illuminate\Support\Str;
use App\Core\Repositories\Interfaces\PageRepositoryInterface;

class SlugService
{
    public function __construct(protected PageRepositoryInterface $repository) {}

    public function generate(string $title, ?int $ignoreId = null, string $locale = 'tr'): string
    {
        $slug = Str::slug($title);
        $originalSlug = $slug;
        $count = 1;

        while ($this->isReserved($slug) || $this->exists($slug, $ignoreId)) {
            $slug = "{$originalSlug}-{$count}";
            $count++;
        }

        return $slug;
    }

    public function validateUnique(string $slug, ?int $ignoreId = null): bool
    {
        if ($this->isReserved($slug)) {
            return false;
        }
        return !$this->exists($slug, $ignoreId);
    }

    protected function isReserved(string $slug): bool
    {
        $reserved = config('cms.reserved_slugs', []);
        return in_array(strtolower($slug), $reserved, true);
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
