<?php

namespace App\DTOs\Admission;

class UpdateAdmissionDTO
{
    public function __construct(
        public string $firstName,
        public string $lastName,
        public string $phone,
        public ?int $branchId = null,
        public ?int $advisorId = null,
        public ?string $tcNo = null,
        public ?string $email = null,
        public ?string $guardianName = null,
        public ?string $guardianPhone = null,
        public ?string $guardianTcNo = null,
        public ?string $school = null,
        public ?string $grade = null,
        public ?string $program = null,
        public float $totalAmount = 0.00,
        public float $depositAmount = 0.00,
        public ?string $status = null,
        public ?string $notes = null
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            $data['first_name'],
            $data['last_name'],
            $data['phone'],
            isset($data['branch_id']) ? (int) $data['branch_id'] : null,
            isset($data['advisor_id']) ? (int) $data['advisor_id'] : null,
            $data['tc_no'] ?? null,
            $data['email'] ?? null,
            $data['guardian_name'] ?? null,
            $data['guardian_phone'] ?? null,
            $data['guardian_tc_no'] ?? null,
            $data['school'] ?? null,
            $data['grade'] ?? null,
            $data['program'] ?? null,
            isset($data['total_amount']) ? (float) $data['total_amount'] : 0.00,
            isset($data['deposit_amount']) ? (float) $data['deposit_amount'] : 0.00,
            $data['status'] ?? null,
            $data['notes'] ?? null
        );
    }

    public function toArray(): array
    {
        $arr = [
            'first_name' => $this->firstName,
            'last_name' => $this->lastName,
            'phone' => $this->phone,
            'branch_id' => $this->branchId,
            'advisor_id' => $this->advisorId,
            'tc_no' => $this->tcNo,
            'email' => $this->email,
            'guardian_name' => $this->guardianName,
            'guardian_phone' => $this->guardianPhone,
            'guardian_tc_no' => $this->guardianTcNo,
            'school' => $this->school,
            'grade' => $this->grade,
            'program' => $this->program,
            'total_amount' => $this->totalAmount,
            'deposit_amount' => $this->depositAmount,
            'notes' => $this->notes,
        ];
        if ($this->status !== null) {
            $arr['status'] = $this->status;
        }
        return $arr;
    }
}
