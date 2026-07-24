<?php

namespace App\DTOs\Admission;

class CreateContractDTO
{
    public function __construct(
        public int $studentAdmissionId,
        public int $contractTemplateId
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            (int) $data['student_admission_id'],
            (int) $data['contract_template_id']
        );
    }
}
