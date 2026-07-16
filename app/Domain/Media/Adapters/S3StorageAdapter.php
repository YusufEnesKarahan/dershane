<?php
namespace App\Domain\Media\Adapters;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class S3StorageAdapter implements StorageAdapterInterface
{
    protected string $disk = 's3';

    public function put(string $path, UploadedFile $file, string $filename): bool
    {
        return Storage::disk($this->disk)->putFileAs($path, $file, $filename) !== false;
    }

    public function putContent(string $path, string $filename, string $content): bool
    {
        return Storage::disk($this->disk)->put($path . '/' . $filename, $content);
    }

    public function delete(string $path): bool
    {
        return Storage::disk($this->disk)->delete($path);
    }

    public function getUrl(string $path): string
    {
        return Storage::disk($this->disk)->url($path);
    }

    public function getTemporaryUrl(string $path, int $expiresInMinutes): string
    {
        return Storage::disk($this->disk)->temporaryUrl($path, now()->addMinutes($expiresInMinutes));
    }
}
