<?php
namespace App\Domain\Media\Preview;

use App\Models\Media;

class PdfPreviewGenerator implements PreviewGeneratorInterface
{
    public function supports(string $mimeType): bool
    {
        return $mimeType === 'application/pdf';
    }

    public function generatePreview(Media $media): ?string
    {
        // PDF thumbnail preview generation helper
        return null;
    }
}
