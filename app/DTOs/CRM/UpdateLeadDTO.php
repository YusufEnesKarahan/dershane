<?php

namespace App\DTOs\CRM;

class UpdateLeadDTO
{
    public function __construct(
        public string $firstName,
        public string $lastName,
        public string $phone,
        public ?string $whatsapp = null,
        public ?string $email = null,
        public ?string $school = null,
        public ?string $grade = null,
        public ?string $city = null,
        public ?string $district = null,
        public ?string $address = null,
        public ?string $guardianInfo = null,
        public ?string $program = null,
        public ?string $reference = null,
        public ?int $leadSourceId = null,
        public ?int $leadStatusId = null,
        public ?int $branchId = null,
        public ?int $advisorId = null
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            $data['first_name'],
            $data['last_name'],
            $data['phone'],
            $data['whatsapp'] ?? null,
            $data['email'] ?? null,
            $data['school'] ?? null,
            $data['grade'] ?? null,
            $data['city'] ?? null,
            $data['district'] ?? null,
            $data['address'] ?? null,
            $data['guardian_info'] ?? null,
            $data['program'] ?? null,
            $data['reference'] ?? null,
            isset($data['lead_source_id']) ? (int) $data['lead_source_id'] : null,
            isset($data['lead_status_id']) ? (int) $data['lead_status_id'] : null,
            isset($data['branch_id']) ? (int) $data['branch_id'] : null,
            isset($data['advisor_id']) ? (int) $data['advisor_id'] : null
        );
    }

    public function toArray(): array
    {
        return [
            'first_name' => $this->firstName,
            'last_name' => $this->lastName,
            'phone' => $this->phone,
            'whatsapp' => $this->whatsapp,
            'email' => $this->email,
            'school' => $this->school,
            'grade' => $this->grade,
            'city' => $this->city,
            'district' => $this->district,
            'address' => $this->address,
            'guardian_info' => $this->guardianInfo,
            'program' => $this->program,
            'reference' => $this->reference,
            'lead_source_id' => $this->leadSourceId,
            'lead_status_id' => $this->leadStatusId,
            'branch_id' => $this->branchId,
            'advisor_id' => $this->advisorId,
        ];
    }
}
