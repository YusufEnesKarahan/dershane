<?php

namespace App\Domain\Onboarding\Services;

use App\Models\Branch;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\Classroom;
use App\Models\Course;
use App\Models\Exam;
use App\Models\ExamResult;
use App\Models\User;
use App\Models\Role;
use App\Domain\Student\Services\StudentManagementService;
use App\Domain\Teacher\Services\TeacherManagementService;
use App\Domain\Exam\Services\ExamResultService;
use Illuminate\Support\Str;

class DemoDataSeederService
{
    public function __construct(
        protected StudentManagementService $studentService,
        protected TeacherManagementService $teacherService,
        protected ExamResultService $examResultService
    ) {}

    public function seed(int $branchId, int $adminId): void
    {
        // 1. Create Classrooms
        $classrooms = [];
        foreach (['10-A', '11-A', '12-A'] as $cName) {
            $classrooms[] = Classroom::create([
                'branch_id' => $branchId,
                'name' => $cName,
                'code' => Str::slug($cName),
                'capacity' => 30,
                'is_active' => true,
            ]);
        }

        // 2. Create Teachers
        $teachersData = [
            ['first_name' => 'Ahmet', 'last_name' => 'Yılmaz', 'email' => 'ahmet.yilmaz@demo.com'],
            ['first_name' => 'Mehmet', 'last_name' => 'Kaya', 'email' => 'mehmet.kaya@demo.com'],
            ['first_name' => 'Ayşe', 'last_name' => 'Şahin', 'email' => 'ayse.sahin@demo.com'],
        ];

        $teachers = [];
        foreach ($teachersData as $tData) {
            $teachers[] = $this->teacherService->createTeacher(array_merge($tData, [
                'status' => 'Active',
                'title' => 'Öğretmen',
                'experience_years' => 5,
            ]), $branchId, $adminId);
        }

        // 3. Create Courses
        $coursesData = ['Matematik', 'Fizik', 'Kimya', 'Biyoloji', 'Türkçe'];
        $courses = [];
        foreach ($coursesData as $cName) {
            $courses[] = Course::create([
                'branch_id' => $branchId,
                'name' => $cName,
                'code' => Str::slug($cName),
                'slug' => Str::slug($cName),
                'description' => "Demo {$cName} dersi.",
                'is_active' => true,
            ]);
        }

        // 4. Create Students
        $studentsData = [
            ['first_name' => 'Can', 'last_name' => 'Demir'],
            ['first_name' => 'Elif', 'last_name' => 'Çelik'],
            ['first_name' => 'Burak', 'last_name' => 'Öztürk'],
            ['first_name' => 'Selin', 'last_name' => 'Arslan'],
            ['first_name' => 'Deniz', 'last_name' => 'Koç'],
            ['first_name' => 'Murat', 'last_name' => 'Bulut'],
            ['first_name' => 'Aslı', 'last_name' => 'Güler'],
            ['first_name' => 'Hakan', 'last_name' => 'Aydın'],
            ['first_name' => 'Gizem', 'last_name' => 'Yıldız'],
            ['first_name' => 'Serkan', 'last_name' => 'Polat'],
        ];

        $students = [];
        $i = 1;
        foreach ($studentsData as $sData) {
            $classroom = $classrooms[$i % count($classrooms)];
            $students[] = $this->studentService->createStudent([
                'student_number' => 'DEMO-' . (100 + $i),
                'identity_number' => '100000000' . (10 + $i),
                'first_name' => $sData['first_name'],
                'last_name' => $sData['last_name'],
                'classroom_id' => $classroom->id,
                'status' => 'Active',
                'guardian_name' => $sData['first_name'] . ' Veli',
                'guardian_phone' => '55500000' . (10 + $i),
            ], $branchId, $adminId);
            $i++;
        }

        // 5. Create Exam & Results
        $exam = Exam::create([
            'branch_id' => $branchId,
            'title' => 'Matematik Tarama Sınavı',
            'type' => 'tarama',
            'exam_date' => now()->toDateString(),
            'total_score' => 100,
            'status' => 'published',
            'created_by' => $adminId,
        ]);

        foreach ($students as $student) {
            $this->examResultService->submitResult($exam, [
                'student_id' => $student->id,
                'score' => rand(60, 100),
                'correct_answers' => rand(15, 20),
                'wrong_answers' => rand(0, 5),
                'empty_answers' => rand(0, 2),
            ]);
        }
    }
}
