<?php

namespace App\DTOs\Reporting;

class ReportScheduleDTO
{
    public function __construct(
        public string $reportType,
        public string $format,
        public string $emailRecipients,
        public string $cronExpression,
        public bool $isActive = true
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            $data['report_type'],
            $data['format'],
            $data['email_recipients'],
            $data['cron_expression'],
            (bool) ($data['is_active'] ?? true)
        );
    }

    public function toArray(): array
    {
        return [
            'report_type' => $this->reportType,
            'format' => $this->format,
            'email_recipients' => $this->emailRecipients,
            'cron_expression' => $this->cronExpression,
            'is_active' => $this->isActive,
        ];
    }
}
