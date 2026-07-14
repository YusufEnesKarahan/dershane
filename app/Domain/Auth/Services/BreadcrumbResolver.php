<?php
namespace App\Domain\Auth\Services;

class BreadcrumbResolver
{
    public function resolve(): array
    {
        return [
            ['title' => 'Dashboard', 'url' => route('admin.dashboard')]
        ];
    }
}
