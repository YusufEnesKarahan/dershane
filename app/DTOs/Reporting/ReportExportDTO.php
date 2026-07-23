<?php

namespace App\DTOs\Reporting;

class ReportExportDTO
{
    public function __construct(
        public string $reportType,
        public string $format,
        public ?int $requestedBy = null
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            $data['report_type'],
            $data['format'],
            $data['requested_by'] ?? null
        );
    }

    public function toArray(): array
    {
        return [
            'report_type' => $this->reportType,
            'format' => $this->format,
            'requested_by' => $this->requestedBy,
        ];
    }
}
