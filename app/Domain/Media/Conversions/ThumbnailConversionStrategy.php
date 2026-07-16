<?php
namespace App\Domain\Media\Conversions;

use App\Models\Media;
use App\Models\MediaVariant;
use Illuminate\Support\Facades\Storage;

class ThumbnailConversionStrategy implements ConversionStrategyInterface
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
        $width = $info[0];
        $height = $info[1];

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

        $sizes = config('media.image_sizes', []);
        $variants = [];

        foreach ($sizes as $name => $cfg) {
            $targetWidth = $cfg['width'];
            $targetHeight = $cfg['height'] ?? null;

            if ($targetHeight === null) {
                $ratio = $targetWidth / $width;
                $targetHeight = (int) ($height * $ratio);
            }

            $target = imagecreatetruecolor($targetWidth, $targetHeight);

            if ($mime === 'image/png' || $mime === 'image/webp') {
                imagealphablending($target, false);
                imagesavealpha($target, true);
                $transparent = imagecolorallocatealpha($target, 255, 255, 255, 127);
                imagefilledrectangle($target, 0, 0, $targetWidth, $targetHeight, $transparent);
            }

            imagecopyresampled($target, $source, 0, 0, 0, 0, $targetWidth, $targetHeight, $width, $height);

            $conversionDir = $dir . $name;
            if (!Storage::disk($media->disk)->exists($conversionDir)) {
                Storage::disk($media->disk)->makeDirectory($conversionDir);
            }

            $convFullPath = Storage::disk($media->disk)->path($conversionDir . '/' . $media->filename);

            switch ($mime) {
                case 'image/jpeg':
                    imagejpeg($target, $convFullPath, 80);
                    break;
                case 'image/png':
                    imagepng($target, $convFullPath);
                    break;
                case 'image/webp':
                    imagewebp($target, $convFullPath, 80);
                    break;
            }

            imagedestroy($target);

            // Save variant record to database
            $variant = MediaVariant::updateOrCreate([
                'media_id' => $media->id,
                'variant_name' => $name,
            ], [
                'disk' => $media->disk,
                'filename' => $name . '/' . $media->filename,
                'mime_type' => $media->mime_type,
                'size' => filesize($convFullPath),
                'width' => $targetWidth,
                'height' => $targetHeight,
            ]);

            $variants[] = $variant;
        }

        imagedestroy($source);

        return $variants;
    }
}
