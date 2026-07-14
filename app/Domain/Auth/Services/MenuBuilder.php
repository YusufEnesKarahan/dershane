<?php
namespace App\Domain\Auth\Services;

use App\Models\User;

class MenuBuilder
{
    public function __construct(
        protected VisibilityResolver $visibilityResolver,
        protected BadgeResolver $badgeResolver
    ) {}

    public function build(User $user): array
    {
        $rawMenu = config('admin-menu.menu', []);
        $menu = [];

        foreach ($rawMenu as $item) {
            // Check visibility
            if (!$this->visibilityResolver->resolve($user, $item['permission'], $item['edition'] ?? null, $item['feature'] ?? null)) {
                continue;
            }

            $menuItem = [
                'title' => $item['title'],
                'icon' => $item['icon'],
                'route' => $item['route'] ?? null,
                'badge' => $this->badgeResolver->resolve($item),
            ];

            if (isset($item['children'])) {
                $children = [];
                foreach ($item['children'] as $child) {
                    if ($this->visibilityResolver->resolve($user, $child['permission'], $child['edition'] ?? null, $child['feature'] ?? null)) {
                        $children[] = [
                            'title' => $child['title'],
                            'route' => $child['route'],
                            'badge' => $this->badgeResolver->resolve($child),
                        ];
                    }
                }
                if (count($children) > 0) {
                    $menuItem['children'] = $children;
                }
            }

            $menu[] = $menuItem;
        }

        return $menu;
    }
}
