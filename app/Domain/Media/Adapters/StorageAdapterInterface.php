<?php
namespace App\Domain\Media\Adapters;

use Illuminate\Http\UploadedFile;

interface StorageAdapterInterface
{
    public function put(string $path, UploadedFile $file, string $filename): bool;
    public function putContent(string $path, string $filename, string $content): bool;
    public function delete(string $path): bool;
    public function getUrl(string $path): string;
    public function getTemporaryUrl(string $path, int $expiresInMinutes): string;
}
