<?php
namespace App\DTOs\Classroom;

class HolidayDTO
{
    public function __construct(
        public string $name,
        public string $start_date,
        public string $end_date,
        public ?int $branch_id = null,
        public ?string $description = null
    ) {}

    public static function fromRequest(array $data): self
    {
        return new self(
            $data['name'] ?? '',
            $data['start_date'] ?? '',
            $data['end_date'] ?? '',
            isset($data['branch_id']) ? (int) $data['branch_id'] : null,
            $data['description'] ?? null
        );
    }
}
