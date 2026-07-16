<?php
namespace App\Domain\Media\Actions;

use App\Models\Media;
use Illuminate\Support\Facades\Storage;

class DeleteMediaAction
{
    public function execute(Media $media): void
    {
        // Delete physical files
        $dir = $media->directory ? $media->directory . '/' : '';
        
        // Original
        Storage::disk($media->disk)->delete($dir . $media->filename);

        // Conversions
        $conversions = ['thumb', 'small', 'medium', 'large'];
        foreach ($conversions as $conv) {
            Storage::disk($media->disk)->delete($dir . $conv . '/' . $media->filename);
        }

        // Delete from DB
        $media->delete();
    }
}
