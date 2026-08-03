<?php
namespace App\Domain\Student\Services;

use App\Domain\Platform\Services\SubscriptionLimitService;
use App\DTOs\Student\CreateStudentDTO;
use App\DTOs\Student\UpdateStudentDTO;
use App\Core\Repositories\Interfaces\StudentRepositoryInterface;
use App\Models\Branch;
use App\Models\Student;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Validation\ValidationException;

class StudentService
{
    public function __construct(
        protected StudentRepositoryInterface $repository,
        protected SubscriptionLimitService $limitService
    ) {}

    public function create(CreateStudentDTO $dto): Student
    {
        $branch = Branch::query()->findOrFail($dto->branch_id);

        if (!$this->limitService->canAddStudent($branch)) {
            throw new AuthorizationException('Mevcut abonelik planı öğrenci eklemeye izin vermiyor.');
        }

        $exists = $this->repository->findByStudentNumber($dto->student_number);
        if ($exists) {
            throw ValidationException::withMessages([
                'student_number' => 'Student number must be unique system-wide.',
            ]);
        }

        return $this->repository->create([
            'student_number' => $dto->student_number,
            'identity_number' => $dto->identity_number,
            'first_name' => $dto->first_name,
            'last_name' => $dto->last_name,
            'birth_date' => $dto->birth_date,
            'gender' => $dto->gender,
            'branch_id' => $branch->id,
            'classroom_id' => $dto->classroom_id,
            'status' => $dto->status,
        ]);
    }

    public function update(Student $student, UpdateStudentDTO $dto): Student
    {
        return $this->repository->update($student, [
            'identity_number' => $dto->identity_number,
            'first_name' => $dto->first_name,
            'last_name' => $dto->last_name,
            'birth_date' => $dto->birth_date,
            'gender' => $dto->gender,
            'branch_id' => $dto->branch_id,
            'classroom_id' => $dto->classroom_id,
            'status' => $dto->status,
        ]);
    }

    public function transfer(Student $student, int $toBranchId, ?int $toClassroomId, ?string $reason): void
    {
        \App\Models\StudentTransfer::create([
            'student_id' => $student->id,
            'from_branch_id' => $student->branch_id,
            'to_branch_id' => $toBranchId,
            'from_classroom_id' => $student->classroom_id,
            'to_classroom_id' => $toClassroomId,
            'reason' => $reason,
            'transfer_date' => date('Y-m-d'),
        ]);

        $student->update([
            'branch_id' => $toBranchId,
            'classroom_id' => $toClassroomId,
        ]);
    }
}
