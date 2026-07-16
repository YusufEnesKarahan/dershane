<?php
namespace App\Domain\Media\Services;

use Illuminate\Support\Facades\Storage;

class ImageOptimizerService
{
    public function optimize(string $filePath, string $disk): bool
    {
        $fullPath = Storage::disk($disk)->path($filePath);
        if (!file_exists($fullPath)) {
            return false;
        }

        $info = getimagesize($fullPath);
        if (!$info) {
            return false; // Not an image GD can read
        }

        $mime = $info['mime'];
        
        // Load image resource
        $image = null;
        switch ($mime) {
            case 'image/jpeg':
                $image = imagecreatefromjpeg($fullPath);
                break;
            case 'image/png':
                $image = imagecreatefrompng($fullPath);
                break;
            case 'image/webp':
                $image = imagecreatefromwebp($fullPath);
                break;
        }

        if (!$image) {
            return false;
        }

        // Export as WebP to save space / optimize
        $dir = dirname($filePath);
        $filename = pathinfo($filePath, PATHINFO_FILENAME) . '.webp';
        $newWebpPath = ($dir === '.' ? '' : $dir . '/') . $filename;
        $webpFullPath = Storage::disk($disk)->path($newWebpPath);

        imagewebp($image, $webpFullPath, 80);
        imagedestroy($image);

        return true;
    }
}
