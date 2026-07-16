<?php
namespace App\Domain\Media\Services;

use App\DTOs\Media\UploadMediaDTO;
use App\Core\Repositories\Interfaces\MediaRepositoryInterface;
use App\Models\Media;
use App\Jobs\OptimizeImageJob;
use App\Domain\Media\Adapters\StorageAdapterInterface;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;

class MediaService
{
    public function __construct(
        protected MediaRepositoryInterface $repository,
        protected StorageAdapterInterface $storageAdapter
    ) {}

    public function upload(UploadMediaDTO $dto): Media
    {
        $file = $dto->file;
        $checksum = hash_file('sha256', $file->getRealPath());

        // Checksum duplicate detection bypass
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

        // 1. Temporary upload path first (staging area)
        $tempDir = 'temp';
        $filename = uniqid() . '_' . str_replace(' ', '_', $file->getClientOriginalName());
        $this->storageAdapter->put($tempDir, $file, $filename);

        // 2. Move file from staging/temp area to target permanent collection path
        $permanentDir = $dto->collection;
        $tempPath = $tempDir . '/' . $filename;
        $permanentPath = $permanentDir . '/' . $filename;

        \Illuminate\Support\Facades\Storage::disk(config('media.disk', 'public'))->move($tempPath, $permanentPath);

        // Fetch image dimensions
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
            'disk' => config('media.disk', 'public'),
            'directory' => $permanentDir,
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

        // Dispatch background job to prevent blocking the upload request!
        if (str_starts_with($mime, 'image/') && $ext !== 'svg') {
            OptimizeImageJob::dispatch($media);
        }

        return $media;
    }
}
