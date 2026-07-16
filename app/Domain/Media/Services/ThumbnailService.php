<?php
namespace App\Domain\Media\Services;

use Illuminate\Support\Facades\Storage;

class ThumbnailService
{
    public function generate(string $filePath, string $disk): void
    {
        $fullPath = Storage::disk($disk)->path($filePath);
        if (!file_exists($fullPath)) {
            return;
        }

        $info = getimagesize($fullPath);
        if (!$info) {
            return;
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
            return;
        }

        $sizes = config('media.image_sizes', []);
        $dir = dirname($filePath);
        $filename = basename($filePath);

        foreach ($sizes as $name => $cfg) {
            $targetWidth = $cfg['width'];
            $targetHeight = $cfg['height'] ?? null;

            if ($targetHeight === null) {
                // Keep aspect ratio
                $ratio = $targetWidth / $width;
                $targetHeight = (int) ($height * $ratio);
            }

            // Create target empty image canvas
            $target = imagecreatetruecolor($targetWidth, $targetHeight);

            // Handle transparency for PNGs
            if ($mime === 'image/png' || $mime === 'image/webp') {
                imagealphablending($target, false);
                imagesavealpha($target, true);
                $transparent = imagecolorallocatealpha($target, 255, 255, 255, 127);
                imagefilledrectangle($target, 0, 0, $targetWidth, $targetHeight, $transparent);
            }

            // Resample
            imagecopyresampled($target, $source, 0, 0, 0, 0, $targetWidth, $targetHeight, $width, $height);

            // Save thumbnail under directory/name/filename
            $conversionDir = ($dir === '.' ? '' : $dir . '/') . $name;
            if (!Storage::disk($disk)->exists($conversionDir)) {
                Storage::disk($disk)->makeDirectory($conversionDir);
            }

            $convFullPath = Storage::disk($disk)->path($conversionDir . '/' . $filename);

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
        }

        imagedestroy($source);
    }
}
