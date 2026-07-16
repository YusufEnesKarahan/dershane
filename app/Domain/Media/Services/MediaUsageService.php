<?php
namespace App\Domain\Media\Services;

use App\Models\MediaUsage;
use App\Models\Media;

class MediaUsageService
{
    public function logUsage(int $mediaId, string $modelType, int $modelId, string $field): MediaUsage
    {
        $usage = MediaUsage::firstOrCreate([
            'media_id' => $mediaId,
            'model_type' => $modelType,
            'model_id' => $modelId,
            'field' => $field,
        ]);

        $this->updateCount($mediaId);

        return $usage;
    }

    public function removeUsage(int $mediaId, string $modelType, int $modelId, string $field): void
    {
        MediaUsage::where([
            'media_id' => $mediaId,
            'model_type' => $modelType,
            'model_id' => $modelId,
            'field' => $field,
        ])->delete();

        $this->updateCount($mediaId);
    }

    public function getUsages(int $mediaId)
    {
        return MediaUsage::where('media_id', $mediaId)->get();
    }

    protected function updateCount(int $mediaId): void
    {
        $media = Media::find($mediaId);
        if ($media) {
            $count = MediaUsage::where('media_id', $mediaId)->count();
            $media->update(['usage_count' => $count, 'last_used_at' => now()]);
        }
    }
}
