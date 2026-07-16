<?php
namespace App\Observers;

use App\Models\Media;
use App\Core\Contracts\ActivityLoggerInterface;

class MediaObserver
{
    public function __construct(protected ActivityLoggerInterface $logger) {}

    public function creating(Media $media)
    {
        if (empty($media->uuid)) {
            $media->uuid = (string) \Illuminate\Support\Str::uuid();
        }
    }

    public function created(Media $media)
    {
        $this->logger->log('Media Uploaded', ['media_id' => $media->id, 'filename' => $media->filename]);
        event(new \App\Events\Media\MediaUploaded($media));
    }

    public function updated(Media $media)
    {
        if ($media->isDirty('folder_id')) {
            $this->logger->log('Media Moved', ['media_id' => $media->id, 'folder_id' => $media->folder_id]);
            event(new \App\Events\Media\MediaMoved($media));
        }
        if ($media->isDirty('title')) {
            $this->logger->log('Media Renamed', ['media_id' => $media->id, 'title' => $media->title]);
            event(new \App\Events\Media\MediaRenamed($media));
        }
    }

    public function deleted(Media $media)
    {
        $this->logger->log('Media Deleted', ['media_id' => $media->id, 'filename' => $media->filename]);
        event(new \App\Events\Media\MediaDeleted($media));
    }
}
