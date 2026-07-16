<?php
namespace App\Domain\Media\Cache;

use App\Models\Media;

interface MediaCacheInvalidatorInterface
{
    public function invalidate(Media $media): void;
}
