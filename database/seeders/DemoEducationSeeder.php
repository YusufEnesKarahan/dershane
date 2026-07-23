<?php
namespace Database\Seeders;
use Illuminate\Database\Seeder;
use App\Models\{Teacher, Course, Classroom, User, Branch};

class DemoEducationSeeder extends Seeder {
    public function run(): void {
        $branch = Branch::first();
        $user = User::factory()->create(['name' => 'Kemal Yıldız', 'email' => 'teacher@dershane.com', 'password' => bcrypt('password')]);
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

        $stPresent = \App\Models\AttendanceStatus::create([
            'code' => 'PRESENT',
            'name' => 'Katıldı',
            'color_code' => '#10B981',
            'is_absence' => false
        ]);

        \App\Models\AttendanceStatus::create([
            'code' => 'LATE',
            'name' => 'Geç Kaldı',
            'color_code' => '#F59E0B',
            'is_absence' => false
        ]);

        \App\Models\AttendanceStatus::create([
            'code' => 'ABSENT',
            'name' => 'Devamsız',
            'color_code' => '#EF4444',
            'is_absence' => true
        ]);

        \App\Models\AttendanceStatus::create([
            'code' => 'EXCUSED',
            'name' => 'İzinli / Mazeretli',
            'color_code' => '#3B82F6',
            'is_absence' => false
        ]);

        $session = \App\Models\AttendanceSession::create([
            'classroom_id' => $classroom->id,
            'course_id' => $course->id,
            'teacher_id' => $teacher->id,
            'session_date' => date('Y-m-d'),
            'start_time' => '09:00:00',
            'end_time' => '10:30:00',
            'status' => 'Completed'
        ]);

        \App\Models\Attendance::create([
            'attendance_session_id' => $session->id,
            'student_id' => $student->id,
            'attendance_status_id' => $stPresent->id,
            'qr_code_scanned' => true,
            'check_in_time' => now()
        ]);

        $exam = \App\Models\Exam::create([
            'title' => 'TYT Türkiye Geneli Deneme - 1',
            'code' => 'TYT-2026-01',
            'exam_type' => 'TYT',
            'exam_date' => date('Y-m-d'),
            'total_questions' => 120,
            'duration_minutes' => 135,
            'is_published' => true
        ]);

        \App\Models\ExamResult::create([
            'exam_id' => $exam->id,
            'student_id' => $student->id,
            'total_correct' => 90,
            'total_wrong' => 20,
            'total_empty' => 10,
            'total_net' => 85.00,
            'score' => 354.17,
            'branch_rank' => 1,
            'global_rank' => 1,
            'is_absent' => false
        ]);

        $assignment = \App\Models\Assignment::create([
            'title' => '12-A Matematik Türev Problem Seti',
            'code' => 'ODV-2026-01',
            'description' => 'Sayfa 140-155 arası türev alma kuralları ve teğet denklemi soruları çözülecektir.',
            'assignment_type' => 'Classroom',
            'classroom_id' => $classroom->id,
            'teacher_id' => $teacher->id,
            'due_date' => date('Y-m-d H:i:s', strtotime('+7 days')),
            'max_score' => 100,
            'status' => 'Published'
        ]);

        $submission = \App\Models\AssignmentSubmission::create([
            'assignment_id' => $assignment->id,
            'student_id' => $student->id,
            'submission_date' => now(),
            'is_late' => false,
            'remarks' => 'Tüm sorular eksiksiz çözülüp teslim edildi.',
            'status' => 'Graded'
        ]);

        \App\Models\AssignmentScore::create([
            'submission_id' => $submission->id,
            'evaluator_id' => 1,
            'score' => 95.00,
            'max_score' => 100.00,
            'feedback' => 'Türev kuralları ve teğet eğimi analizleri çok başarılı.'
        ]);

        $pmCash = \App\Models\PaymentMethod::create([
            'name' => 'Nakit',
            'code' => 'cash',
            'is_active' => true
        ]);

        \App\Models\PaymentMethod::create([
            'name' => 'Kredi Kartı',
            'code' => 'credit_card',
            'is_active' => true
        ]);

        \App\Models\PaymentMethod::create([
            'name' => 'Banka Havalesi / EFT',
            'code' => 'bank_transfer',
            'is_active' => true
        ]);

        $invoice = \App\Models\Invoice::create([
            'invoice_number' => 'INV-2026-001',
            'student_id' => $student->id,
            'issue_date' => date('Y-m-d'),
            'due_date' => date('Y-m-d', strtotime('+30 days')),
            'total_amount' => 25000.00,
            'paid_amount' => 10000.00,
            'status' => 'Partial'
        ]);

        \App\Models\InvoiceItem::create([
            'invoice_id' => $invoice->id,
            'description' => '2026-2027 Eğitim Dönemi Kurs Kayıt Ücreti',
            'quantity' => 1,
            'unit_price' => 25000.00,
            'total_price' => 25000.00
        ]);

        \App\Models\Payment::create([
            'payment_number' => 'PAY-2026-001',
            'invoice_id' => $invoice->id,
            'student_id' => $student->id,
            'payment_method_id' => $pmCash->id,
            'amount' => 10000.00,
            'payment_date' => now(),
            'notes' => 'Peşinat tahsil edildi.',
            'status' => 'Completed'
        ]);

        \App\Models\StudentDebt::create([
            'student_id' => $student->id,
            'invoice_id' => $invoice->id,
            'amount' => 25000.00,
            'remaining_amount' => 15000.00,
            'due_date' => date('Y-m-d', strtotime('+30 days')),
            'status' => 'Partial'
        ]);

        \App\Models\SmsProvider::create([
            'name' => 'NetGSM',
            'code' => 'netgsm',
            'is_active' => true
        ]);

        \App\Models\MailTemplate::create([
            'name' => 'Eğitim Kayıt E-Postası',
            'subject' => 'Kurs Kaydınız Başarıyla Tamamlandı',
            'body_html' => 'Merhaba {user_name}, dershane kaydınız başarıyla tamamlanmıştır.'
        ]);

        \App\Models\NotificationTemplate::create([
            'code' => 'invoice_due',
            'title' => 'Fatura Vadesi Yaklaştı',
            'body' => 'Değerli velimiz, faturanızın son ödeme tarihi yaklaşmaktadır. Kalan tutar: {amount}',
            'channel' => 'System'
        ]);

        \App\Models\Notification::create([
            'user_id' => 1,
            'title' => 'Dershane Sistemine Hoş Geldiniz',
            'content' => 'Eğitim portalı kullanımınız başarıyla açılmıştır.',
            'type' => 'System',
            'status' => 'Unread'
        ]);

        $group = \App\Models\AnnouncementGroup::create([
            'name' => 'Tüm Öğrenciler',
            'code' => 'all_students'
        ]);

        \App\Models\Announcement::create([
            'title' => '29 Ekim Cumhuriyet Bayramı Tatili Hakkında',
            'content' => '29 Ekim Cumhuriyet Bayramı resmi tatili nedeniyle dershanemizde eğitim yapılmayacaktır.',
            'announcement_group_id' => $group->id,
            'is_published' => true,
            'published_at' => now()
        ]);

        $parent = \App\Models\User::create([
            'name' => 'Fatma Yılmaz',
            'email' => 'parent@dershane.com',
            'password' => bcrypt('password'),
        ]);

        // Link parent to student
        \App\Models\ParentStudent::create([
            'parent_id' => $parent->id,
            'student_id' => $student->id,
            'relation_type' => 'Mother'
        ]);

        \App\Models\ParentNotification::create([
            'parent_id' => $parent->id,
            'title' => 'Öğrencinizin Devamsızlık Bildirimi',
            'content' => 'Öğrenciniz Ahmet Yılmaz bugün 1. ders saatinde devamsız olarak işaretlenmiştir.',
            'is_read' => false
        ]);

        \App\Models\TeacherProfile::create([
            'teacher_id' => $teacher->id,
            'bio_extended' => 'Uzun yıllar YKS hazırlık gruplarında çalıştı.',
            'office_hours' => 'Pazartesi 14:00 - 16:00',
            'room_number' => 'Derslik 101'
        ]);

        \App\Models\TeacherSubject::create([
            'teacher_id' => $teacher->id,
            'subject_name' => 'Matematik',
            'code' => 'MAT'
        ]);

        \App\Models\TeacherAssignment::create([
            'teacher_id' => $teacher->id,
            'classroom_id' => $classroom->id,
            'course_id' => $course->id
        ]);

        \App\Models\TeacherSchedule::create([
            'teacher_id' => $teacher->id,
            'classroom_id' => $classroom->id,
            'course_id' => $course->id,
            'date' => date('Y-m-d'),
            'start_time' => '10:00:00',
            'end_time' => '11:30:00'
        ]);

        \App\Models\TeacherPerformanceLog::create([
            'teacher_id' => $teacher->id,
            'metric_type' => 'Student Success Rate',
            'score' => 95.50,
            'comments' => 'YKS başarı ortalaması çok yüksek.',
            'evaluated_at' => now()
        ]);

        \App\Models\DashboardSnapshot::create([
            'metrics' => [
                'student_count' => 12,
                'teacher_count' => 4,
                'branch_count' => 1,
                'today_lessons' => 3,
                'today_attendance_sessions' => 2,
                'absence_rate' => 5.5,
                'avg_tyt_net' => 78.50,
                'avg_ayt_net' => 54.20,
                'total_collected' => 15000.00,
                'pending_debt' => 10000.00,
                'total_submissions' => 8,
                'total_notifications' => 12,
                'calculated_at' => now()->toDateTimeString(),
            ]
        ]);

        \App\Models\ReportSchedule::create([
            'report_type' => 'Weekly Manager Summary',
            'format' => 'PDF',
            'email_recipients' => 'director@dershane.com',
            'cron_expression' => '0 9 * * 1',
            'is_active' => true
        ]);

        \App\Models\ExecutiveReport::create([
            'title' => '2026 Güz Dönemi Performans Analizi',
            'description' => 'Güz dönemi öğrenci devamlılık durumları ve deneme sınavları ortalama başarı grafikleri.',
            'content_data' => [
                'success_rate' => '88%',
                'absence_rate' => '5.5%'
            ]
        ]);
    }
}
