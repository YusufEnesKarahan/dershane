<?php
namespace App\Domain\Media\Preview;

use App\Models\Media;

interface PreviewGeneratorInterface
{
    public function supports(string $mimeType): bool;
    public function generatePreview(Media $media): ?string;
}
