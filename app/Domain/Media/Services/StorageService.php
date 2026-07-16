<?php
namespace App\Domain\Media\Services;

use Illuminate\Support\Facades\Storage;
use Illuminate\Http\UploadedFile;

class StorageService
{
    public function getDisk(): string
    {
        return config('media.disk', 'public');
    }

    public function putFile(string $path, UploadedFile $file): string
    {
        $disk = $this->getDisk();
        $filename = uniqid() . '_' . str_replace(' ', '_', $file->getClientOriginalName());
        
        Storage::disk($disk)->putFileAs($path, $file, $filename);
        
        return $filename;
    }

    public function putContent(string $path, string $filename, string $content): void
    {
        Storage::disk($this->getDisk())->put($path . '/' . $filename, $content);
    }

    public function delete(string $path): bool
    {
        return Storage::disk($this->getDisk())->delete($path);
    }

    public function deleteDirectory(string $path): bool
    {
        return Storage::disk($this->getDisk())->deleteDirectory($path);
    }
}
