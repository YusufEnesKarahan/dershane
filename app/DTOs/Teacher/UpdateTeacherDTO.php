<?php
namespace App\DTOs\Teacher;

class UpdateTeacherDTO
{
    public function __construct(
        public ?int $branch_id = null,
        public ?string $title = null,
        public ?string $bio = null,
        public ?string $specialties = null,
        public ?string $education = null,
        public int $experience_years = 0,
        public ?string $emergency_contact = null,
        public string $status = 'Active'
    ) {}

    public static function fromRequest(array $data): self
    {
        return new self(
            isset($data['branch_id']) ? (int) $data['branch_id'] : null,
            $data['title'] ?? null,
            $data['bio'] ?? null,
            $data['specialties'] ?? null,
            $data['education'] ?? null,
            (int) ($data['experience_years'] ?? 0),
            $data['emergency_contact'] ?? null,
            $data['status'] ?? 'Active'
        );
    }
}
