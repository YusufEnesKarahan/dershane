<?php
namespace App\Domain\CMS\Services;

interface MarkdownRendererInterface
{
    public function render(string $markdown): string;
}
