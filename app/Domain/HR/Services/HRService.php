<?php

namespace App\Domain\HR\Services;

use App\Core\Repositories\Interfaces\EmployeeRepositoryInterface;
use App\Core\Repositories\Interfaces\DepartmentRepositoryInterface;
use App\DTOs\HR\CreateEmployeeDTO;
use App\DTOs\HR\UpdateEmployeeDTO;

class HRService
{
    public function __construct(
        protected EmployeeRepositoryInterface $employeeRepo,
        protected DepartmentRepositoryInterface $departmentRepo
    ) {}

    public function allEmployees()
    {
        return $this->employeeRepo->all();
    }

    public function findEmployee(int $id)
    {
        return $this->employeeRepo->find($id);
    }

    public function createEmployee(CreateEmployeeDTO $dto)
    {
        return $this->employeeRepo->create([
            'employee_no' => $dto->employeeNo,
            'user_id' => $dto->userId,
            'department_id' => $dto->departmentId,
            'position_id' => $dto->positionId,
            'first_name' => $dto->firstName,
            'last_name' => $dto->lastName,
            'tc_no' => $dto->tcNo,
            'birth_date' => $dto->birthDate,
            'gender' => $dto->gender,
            'phone' => $dto->phone,
            'email' => $dto->email,
            'address' => $dto->address,
            'hire_date' => $dto->hireDate,
            'contract_type' => $dto->contractType,
            'employment_status' => $dto->employmentStatus,
            'salary' => $dto->salary,
            'iban' => $dto->iban,
            'emergency_contact' => $dto->emergencyContact,
            'emergency_phone' => $dto->emergencyPhone,
            'notes' => $dto->notes
        ]);
    }

    public function updateEmployee(int $id, UpdateEmployeeDTO $dto)
    {
        return $this->employeeRepo->update($id, [
            'department_id' => $dto->departmentId,
            'position_id' => $dto->positionId,
            'first_name' => $dto->firstName,
            'last_name' => $dto->lastName,
            'tc_no' => $dto->tcNo,
            'birth_date' => $dto->birthDate,
            'gender' => $dto->gender,
            'phone' => $dto->phone,
            'email' => $dto->email,
            'address' => $dto->address,
            'contract_type' => $dto->contractType,
            'employment_status' => $dto->employmentStatus,
            'salary' => $dto->salary,
            'iban' => $dto->iban,
            'emergency_contact' => $dto->emergencyContact,
            'emergency_phone' => $dto->emergencyPhone,
            'notes' => $dto->notes
        ]);
    }

    public function terminateEmployee(int $id)
    {
        return $this->employeeRepo->update($id, [
            'employment_status' => 'Terminated'
        ]);
    }

    public function allDepartments()
    {
        return $this->departmentRepo->all();
    }
}
