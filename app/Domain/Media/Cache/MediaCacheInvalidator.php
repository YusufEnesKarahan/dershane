<?php
namespace App\Domain\Media\Cache;

use App\Models\Media;
use Illuminate\Support\Facades\Cache;

class MediaCacheInvalidator implements MediaCacheInvalidatorInterface
{
    public function invalidate(Media $media): void
    {
        Cache::forget('media.url.' . $media->uuid);
        // Standardized Cloudflare / AWS CloudFront purge call stubs
    }
}
