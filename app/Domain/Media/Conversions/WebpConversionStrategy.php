<?php
namespace App\Domain\Media\Conversions;

use App\Models\Media;
use App\Models\MediaVariant;
use Illuminate\Support\Facades\Storage;

class WebpConversionStrategy implements ConversionStrategyInterface
{
    public function canConvert(string $mimeType): bool
    {
        return str_starts_with($mimeType, 'image/') && $mimeType !== 'image/svg+xml';
    }

    public function convert(Media $media): array
    {
        $dir = $media->directory ? $media->directory . '/' : '';
        $fullPath = Storage::disk($media->disk)->path($dir . $media->filename);
        if (!file_exists($fullPath)) {
            return [];
        }

        $info = getimagesize($fullPath);
        if (!$info) {
            return [];
        }

        $mime = $info['mime'];

        $source = null;
        switch ($mime) {
            case 'image/jpeg':
                $source = imagecreatefromjpeg($fullPath);
                break;
            case 'image/png':
                $source = imagecreatefrompng($fullPath);
                break;
            case 'image/webp':
                $source = imagecreatefromwebp($fullPath);
                break;
        }

        if (!$source) {
            return [];
        }

        // WebP format output (GD automatically strips EXIF metadata on imagewebp write!)
        $webpFilename = pathinfo($media->filename, PATHINFO_FILENAME) . '.webp';
        
        $conversionDir = $dir . 'webp';
        if (!Storage::disk($media->disk)->exists($conversionDir)) {
            Storage::disk($media->disk)->makeDirectory($conversionDir);
        }

        $webpFullPath = Storage::disk($media->disk)->path($conversionDir . '/' . $webpFilename);
        
        imagewebp($source, $webpFullPath, 80);
        imagedestroy($source);

        $variant = MediaVariant::updateOrCreate([
            'media_id' => $media->id,
            'variant_name' => 'webp',
        ], [
            'disk' => $media->disk,
            'filename' => 'webp/' . $webpFilename,
            'mime_type' => 'image/webp',
            'size' => filesize($webpFullPath),
            'width' => $media->width,
            'height' => $media->height,
        ]);

        return [$variant];
    }
}
