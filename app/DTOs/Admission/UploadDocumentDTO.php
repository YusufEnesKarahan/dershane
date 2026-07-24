<?php

namespace App\DTOs\Admission;

class UploadDocumentDTO
{
    public function __construct(
        public int $studentAdmissionId,
        public string $documentType,
        public string $fileName,
        public string $filePath,
        public ?int $uploadedBy = null
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            (int) $data['student_admission_id'],
            $data['document_type'],
            $data['file_name'],
            $data['file_path'],
            isset($data['uploaded_by']) ? (int) $data['uploaded_by'] : null
        );
    }

    public function toArray(): array
    {
        return [
            'student_admission_id' => $this->studentAdmissionId,
            'document_type' => $this->documentType,
            'file_name' => $this->fileName,
            'file_path' => $this->filePath,
            'uploaded_by' => $this->uploadedBy,
            'status' => 'pending',
        ];
    }
}
