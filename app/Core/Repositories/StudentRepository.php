<?php
namespace App\Core\Repositories;

use App\Models\Student;
use App\Core\Repositories\Interfaces\StudentRepositoryInterface;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class StudentRepository implements StudentRepositoryInterface
{
    public function paginate(int $perPage = 15, array $filters = []): LengthAwarePaginator
    {
        $query = Student::with(['branch', 'classroom', 'primaryGuardian'])->orderBy('created_at', 'desc');

        if (!empty($filters['search'])) {
            $query->where(function ($q) use ($filters) {
                $q->where('first_name', 'like', "%{$filters['search']}%")
                  ->orWhere('last_name', 'like', "%{$filters['search']}%")
                  ->orWhere('student_number', 'like', "%{$filters['search']}%");
            });
        }

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        return $query->paginate($perPage);
    }

    public function all(): Collection
    {
        return Student::with(['branch', 'classroom'])->get();
    }

    public function findById(int $id): ?Student
    {
        return Student::with(['branch', 'classroom', 'guardians', 'contact', 'address', 'enrollments.course', 'documents', 'transfers'])->find($id);
    }

    public function findByStudentNumber(string $number): ?Student
    {
        return Student::where('student_number', $number)->first();
    }

    public function create(array $data): Student
    {
        return Student::create($data);
    }

    public function update(Student $student, array $data): Student
    {
        $student->update($data);
        return $student;
    }

    public function delete(Student $student): bool
    {
        return $student->delete();
    }
}
