<?php
namespace App\DTOs\Student;

class UpdateStudentDTO
{
    public function __construct(
        public string $first_name,
        public string $last_name,
        public int $branch_id,
        public ?string $identity_number = null,
        public ?string $birth_date = null,
        public ?string $gender = null,
        public ?int $classroom_id = null,
        public string $status = 'Active'
    ) {}

    public static function fromRequest(array $data): self
    {
        return new self(
            $data['first_name'] ?? '',
            $data['last_name'] ?? '',
            (int) ($data['branch_id'] ?? 1),
            $data['identity_number'] ?? null,
            $data['birth_date'] ?? null,
            $data['gender'] ?? null,
            isset($data['classroom_id']) ? (int) $data['classroom_id'] : null,
            $data['status'] ?? 'Active'
        );
    }
}
