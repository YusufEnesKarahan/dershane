<?php
namespace App\Domain\Auth\Services;

use Illuminate\Support\Facades\Auth;

class AdminMenuService
{
    public function __construct(
        protected MenuBuilder $menuBuilder,
        protected BreadcrumbResolver $breadcrumbResolver
    ) {}

    public function getSidebarMenu(): array
    {
        $user = Auth::user();
        if (!$user) {
            return [];
        }

        return $this->menuBuilder->build($user);
    }

    public function getBreadcrumbs(): array
    {
        return $this->breadcrumbResolver->resolve();
    }
}
