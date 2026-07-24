<?php

namespace App\Domain\Document\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use InvalidArgumentException;

class FileStorageService
{
    protected array $allowedMimes = [
        'pdf', 'doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx',
        'jpg', 'jpeg', 'png', 'gif', 'zip', 'rar', 'txt', 'csv'
    ];

    protected int $maxSizeBytes = 52428800; // 50 MB

    public function validateFile(UploadedFile $file): void
    {
        $extension = strtolower($file->getClientOriginalExtension());
        
        if (!in_array($extension, $this->allowedMimes)) {
            throw new InvalidArgumentException("Desteklenmeyen dosya türü: {$extension}");
        }

        if ($file->getSize() > $this->maxSizeBytes) {
            throw new InvalidArgumentException("Dosya boyutu 50MB sınırını aşıyor.");
        }
    }

    public function store(UploadedFile $file, string $folder = 'documents'): array
    {
        $this->validateFile($file);

        $fileName = Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME)) . '_' . time() . '.' . $file->getClientOriginalExtension();
        $path = $file->storeAs($folder, $fileName, 'public');

        return [
            'file_name' => $file->getClientOriginalName(),
            'file_path' => 'storage/' . $path,
            'file_type' => $file->getClientOriginalExtension(),
            'file_size' => $file->getSize(),
        ];
    }

    public function delete(string $filePath): bool
    {
        $relative = str_replace('storage/', '', $filePath);
        if (Storage::disk('public')->exists($relative)) {
            return Storage::disk('public')->delete($relative);
        }
        return false;
    }
}
