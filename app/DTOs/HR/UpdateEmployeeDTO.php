<?php

namespace App\DTOs\HR;

class UpdateEmployeeDTO
{
    public function __construct(
        public ?int $departmentId,
        public ?int $positionId,
        public string $firstName,
        public string $lastName,
        public ?string $tcNo,
        public ?string $birthDate,
        public ?string $gender,
        public ?string $phone,
        public ?string $email,
        public ?string $address,
        public string $contractType,
        public string $employmentStatus,
        public float $salary,
        public ?string $iban,
        public ?string $emergencyContact,
        public ?string $emergencyPhone,
        public ?string $notes
    ) {}

    public static function fromRequest($request): self
    {
        return new self(
            departmentId: $request->input('department_id') ? (int) $request->input('department_id') : null,
            positionId: $request->input('position_id') ? (int) $request->input('position_id') : null,
            firstName: $request->input('first_name'),
            lastName: $request->input('last_name'),
            tcNo: $request->input('tc_no'),
            birthDate: $request->input('birth_date'),
            gender: $request->input('gender'),
            phone: $request->input('phone'),
            email: $request->input('email'),
            address: $request->input('address'),
            contractType: $request->input('contract_type', 'Full-time'),
            employmentStatus: $request->input('employment_status', 'Active'),
            salary: (float) $request->input('salary', 0.0),
            iban: $request->input('iban'),
            emergencyContact: $request->input('emergency_contact'),
            emergencyPhone: $request->input('emergency_phone'),
            notes: $request->input('notes')
        );
    }
}
