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

        Classroom::create([
            'name' => '12-A Sayısal',
            'branch_id' => $branch->id,
            'capacity' => 12
        ]);
    }
}
