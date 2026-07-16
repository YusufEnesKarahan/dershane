<?php
namespace App\Domain\Media\Preview;

use App\Models\Media;

class VideoPreviewGenerator implements PreviewGeneratorInterface
{
    public function supports(string $mimeType): bool
    {
        return str_starts_with($mimeType, 'video/');
    }

    public function generatePreview(Media $media): ?string
    {
        // FFmpeg video frame extraction preview helper
        return null;
    }
}
