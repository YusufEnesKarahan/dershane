<?php
namespace Database\Seeders;
use Illuminate\Database\Seeder;
use App\Models\{Teacher, Course, Classroom, User, Branch};

class DemoEducationSeeder extends Seeder {
    public function run(): void {
        $branch = Branch::first();
        $user = User::factory()->create(['name' => 'Kemal Yıldız', 'email' => 'kemal@dershane.com']);
        $teacher = Teacher::create([
            'user_id' => $user->id,
            'branch_id' => $branch->id,
            'title' => 'Matematik Zümre Başkanı',
            'bio' => 'Boğaziçi Mezunu, 15+ yıllık tecrübe.',
            'specialties' => 'Matematik, Geometri',
            'education' => 'Boğaziçi Üniversitesi Matematik Öğretmenliği',
            'experience_years' => 15,
            'emergency_contact' => 'Ayşe Yıldız: 0555 555 55 55',
            'status' => 'Active'
        ]);

        \App\Models\TeacherAvailability::create([
            'teacher_id' => $teacher->id,
            'day_of_week' => 1, // Pazartesi
            'start_time' => '09:00:00',
            'end_time' => '17:00:00',
            'is_recurring' => true
        ]);

        \App\Models\TeacherSalaryProfile::create([
            'teacher_id' => $teacher->id,
            'base_salary' => 60000.00,
            'payment_type' => 'Monthly',
            'bonus' => 5000.00,
            'deductions' => 0.00
        ]);

        \App\Models\TeacherContract::create([
            'teacher_id' => $teacher->id,
            'start_date' => now()->subMonths(6),
            'end_date' => now()->addMonths(6),
            'employment_type' => 'Full-time',
            'status' => 'Active'
        ]);

        \App\Models\TeacherPerformance::create([
            'teacher_id' => $teacher->id,
            'attendance_rate' => 99.20,
            'student_satisfaction' => 4.95,
            'lesson_count' => 60,
            'feedback_score' => 4.88,
            'kpi_month' => date('Y-m')
        ]);

        $level = \App\Models\CourseLevel::create([
            'name' => 'YKS (TYT-AYT) Seviyesi',
            'slug' => 'yks-tyt-ayt-seviyesi',
        ]);

        $course = Course::create([
            'code' => 'YKS-2026-FULL',
            'name' => 'YKS (TYT-AYT) Yoğunlaştırılmış Hazırlık',
            'slug' => 'yks-yogunlastirilmis-hazirlik',
            'description' => 'Boğaziçi ve Düzeyli eğitmenler eşliğinde 10 aylık tam müfredat YKS hazırlık programı.',
            'course_level_id' => $level->id,
            'duration' => '10 Ay',
            'capacity' => 120,
            'status' => 'Published',
            'is_active' => true
        ]);

        \App\Models\CoursePricing::create([
            'course_id' => $course->id,
            'price' => 25000.00,
            'currency' => 'TRY',
            'installment_options' => 10
        ]);

        $course->teachers()->attach($teacher->id);
        $course->branches()->attach($branch->id);

        $type = \App\Models\ClassroomType::create([
            'name' => 'Teori & Etüt Sınıfı',
            'slug' => 'teori-etut-sinifi',
            'description' => 'Projeksiyon ve akıllı tahta donanımlı derslik.'
        ]);

        $classroom = Classroom::create([
            'code' => 'KDK-101',
            'name' => '12-A Sayısal Dersliği',
            'branch_id' => $branch->id,
            'classroom_type_id' => $type->id,
            'capacity' => 24,
            'color_code' => '#4F46E5',
            'is_active' => true
        ]);

        $term = \App\Models\AcademicTerm::create([
            'name' => '2026-2027 Güz Dönemi',
            'start_date' => now()->startOfMonth(),
            'end_date' => now()->addMonths(5)->endOfMonth(),
            'is_active' => true
        ]);

        \App\Models\ClassSchedule::create([
            'classroom_id' => $classroom->id,
            'teacher_id' => $teacher->id,
            'course_id' => $course->id,
            'academic_term_id' => $term->id,
            'day_of_week' => 1, // Pazartesi
            'start_time' => '09:00:00',
            'end_time' => '10:30:00',
            'color_code' => '#4F46E5',
            'is_active' => true
        ]);

        \App\Models\Holiday::create([
            'name' => '29 Ekim Cumhuriyet Bayramı',
            'start_date' => '2026-10-29',
            'end_date' => '2026-10-29',
            'branch_id' => $branch->id,
            'description' => 'Resmi Tatil'
        ]);

        $student = \App\Models\Student::create([
            'student_number' => 'OGR-2026-001',
            'identity_number' => '12345678901',
            'first_name' => 'Ahmet',
            'last_name' => 'Yılmaz',
            'birth_date' => '2008-05-14',
            'gender' => 'Erkek',
            'branch_id' => $branch->id,
            'classroom_id' => $classroom->id,
            'status' => 'Active'
        ]);

        \App\Models\StudentGuardian::create([
            'student_id' => $student->id,
            'guardian_name' => 'Mehmet Yılmaz',
            'relation' => 'Baba',
            'phone' => '0532 111 22 33',
            'email' => 'mehmet@example.com',
            'is_primary' => true
        ]);

        \App\Models\StudentEnrollment::create([
            'student_id' => $student->id,
            'course_id' => $course->id,
            'academic_term_id' => $term->id,
            'price_paid' => 25000.00,
            'enrollment_date' => date('Y-m-d'),
            'status' => 'Active'
        ]);
    }
}
