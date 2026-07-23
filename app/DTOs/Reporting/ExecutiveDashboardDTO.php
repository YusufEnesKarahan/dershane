<?php

namespace App\DTOs\Reporting;

class ExecutiveDashboardDTO
{
    public function __construct(
        public array $metrics
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            $data['metrics'] ?? []
        );
    }

    public function toArray(): array
    {
        return [
            'metrics' => $this->metrics,
        ];
    }
}
