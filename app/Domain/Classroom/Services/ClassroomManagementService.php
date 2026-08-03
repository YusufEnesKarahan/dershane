<?php

namespace App\Domain\Classroom\Services;

use App\Models\Classroom;
use App\Models\Student;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ClassroomManagementService
{
    /**
     * Create a new classroom.
     */
    public function createClassroom(array $data, int $branchId): Classroom
    {
        return DB::transaction(function () use ($data, $branchId) {
            $data['branch_id'] = $branchId;
            
            // Auto generate code if not provided
            if (empty($data['code'])) {
                $data['code'] = 'CLS-' . strtoupper(Str::random(6));
            }

            return Classroom::create($data);
        });
    }

    /**
     * Update an existing classroom.
     */
    public function updateClassroom(Classroom $classroom, array $data): Classroom
    {
        return DB::transaction(function () use ($classroom, $data) {
            $classroom->update($data);
            return $classroom;
        });
    }

    /**
     * Delete a classroom.
     */
    public function deleteClassroom(Classroom $classroom): void
    {
        DB::transaction(function () use ($classroom) {
            // First detach students so they aren't left pointing to a deleted class
            Student::where('classroom_id', $classroom->id)->update(['classroom_id' => null]);
            
            // Delete schedules related to this classroom (soft delete or cascade via DB)
            $classroom->schedules()->delete();
            
            $classroom->delete();
        });
    }

    /**
     * Assign a primary teacher to the classroom.
     */
    public function assignTeacher(Classroom $classroom, ?int $teacherId): void
    {
        $classroom->update(['teacher_id' => $teacherId]);
    }

    /**
     * Attach students to the classroom.
     */
    public function attachStudents(Classroom $classroom, array $studentIds): void
    {
        DB::transaction(function () use ($classroom, $studentIds) {
            $classroom->students()->syncWithoutDetaching($studentIds);
        });
    }

    /**
     * Detach students from a classroom.
     */
    public function detachStudents(Classroom $classroom, array $studentIds)
    {
        return DB::transaction(function () use ($classroom, $studentIds) {
            $classroom->students()->detach($studentIds);
        });
    }
}
