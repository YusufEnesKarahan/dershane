<?php
namespace App\Domain\Media\Services;

use App\DTOs\Media\UploadMediaDTO;
use App\Core\Repositories\Interfaces\MediaRepositoryInterface;
use App\Models\Media;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;

class MediaService
{
    public function __construct(
        protected MediaRepositoryInterface $repository,
        protected StorageService $storageService,
        protected ImageOptimizerService $optimizerService,
        protected ThumbnailService $thumbnailService
    ) {}

    public function upload(UploadMediaDTO $dto): Media
    {
        $file = $dto->file;
        $checksum = hash_file('sha256', $file->getRealPath());

        // Checksum lookup to prevent duplicate physical uploads
        $existing = $this->repository->findByChecksum($checksum);
        if ($existing) {
            return $existing;
        }

        // Validate extensions
        $ext = strtolower($file->getClientOriginalExtension());
        $allowed = config('media.allowed_extensions', []);
        if (!in_array($ext, $allowed, true)) {
            abort(422, 'Extension not allowed.');
        }

        // Put file physically
        $dir = $dto->collection;
        $filename = $this->storageService->putFile($dir, $file);

        // Fetch image dimensions if relevant
        $width = null;
        $height = null;
        $mime = $file->getClientMimeType();
        if (str_starts_with($mime, 'image/')) {
            $dims = getimagesize($file->getRealPath());
            if ($dims) {
                $width = $dims[0];
                $height = $dims[1];
            }
        }

        $media = $this->repository->create([
            'uuid' => Str::uuid()->toString(),
            'disk' => $this->storageService->getDisk(),
            'directory' => $dir,
            'filename' => $filename,
            'original_name' => $file->getClientOriginalName(),
            'extension' => $ext,
            'mime_type' => $mime,
            'size' => $file->getSize(),
            'width' => $width,
            'height' => $height,
            'checksum' => $checksum,
            'alt' => $dto->alt,
            'caption' => $dto->caption,
            'title' => $dto->title ?: pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME),
            'description' => $dto->description,
            'folder_id' => $dto->folder_id,
            'uploaded_by' => Auth::id(),
        ]);

        // Optimization & Thumbnail triggers
        if (str_starts_with($mime, 'image/') && $ext !== 'svg') {
            $filePath = $dir . '/' . $filename;
            $this->optimizerService->optimize($filePath, $media->disk);
            $this->thumbnailService->generate($filePath, $media->disk);
        }

        return $media;
    }
}
