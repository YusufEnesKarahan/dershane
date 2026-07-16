<?php
namespace App\Domain\Media\Adapters;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class LocalStorageAdapter implements StorageAdapterInterface
{
    protected string $disk = 'public';

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
        // For local public folder, temporary url behaves as a standard signed link or fallback URL
        return \Illuminate\Support\Facades\URL::temporarySignedRoute('admin.media.index', now()->addMinutes($expiresInMinutes));
    }
}
