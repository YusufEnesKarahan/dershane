<?php
namespace App\Domain\CMS\Services;

use Illuminate\Support\Str;

class CommonMarkRenderer implements MarkdownRendererInterface
{
    public function render(string $markdown): string
    {
        return Str::markdown($markdown);
    }
}
