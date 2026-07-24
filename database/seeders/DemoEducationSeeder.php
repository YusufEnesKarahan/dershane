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

        $initialAdm = \App\Models\StudentAdmission::create([
            'admission_no' => 'ADM-2026-0000',
            'first_name' => $student->first_name,
            'last_name' => $student->last_name,
            'phone' => '0532 111 22 33',
            'tc_no' => $student->identity_number,
            'program' => 'YKS Yoğun Hazırlık',
            'total_amount' => 25000.00,
            'deposit_amount' => 5000.00,
            'status' => 'enrolled',
            'branch_id' => $branch->id,
        ]);

        \App\Models\StudentEnrollment::create([
            'student_admission_id' => $initialAdm->id,
            'student_id' => $student->id,
            'branch_id' => $branch->id,
            'classroom_id' => $classroom->id,
            'enrollment_no' => 'ENR-2026-0000',
            'enrollment_date' => date('Y-m-d'),
            'academic_year' => '2026-2027',
            'final_fee' => 25000.00,
            'status' => 'completed'
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

        // Notification Center: 20 templates and 100 multi-channel samples.
        $notificationUsers = collect([$user]);
        for ($i = 1; $i <= 9; $i++) {
            $notificationUsers->push(User::factory()->create(['name' => 'Bildirim Kullanıcısı '.$i, 'email' => 'notification'.$i.'@dershane.test']));
        }
        $templateTypes = ['student', 'parent', 'teacher', 'employee', 'system', 'finance', 'crm'];
        for ($i = 1; $i <= 20; $i++) {
            $type = $templateTypes[$i % count($templateTypes)];
            \App\Models\NotificationTemplate::create(['name' => ucfirst($type).' bildirim şablonu '.$i, 'slug' => 'demo-'.$type.'-'.$i, 'code' => 'demo_'.$type.'_'.$i, 'title' => ucfirst($type).' bildirimi', 'body' => '{{name}} için otomatik oluşturulan bildirim.', 'title_template' => ucfirst($type).' bildirimi', 'body_template' => '{{name}} için otomatik oluşturulan bildirim.', 'channel' => ['panel', 'email', 'sms'][$i % 3], 'is_active' => true]);
        }
        for ($i = 1; $i <= 100; $i++) {
            $recipient = $notificationUsers[$i % $notificationUsers->count()]; $isRead = $i % 3 !== 0; $createdAt = now()->subDays($i % 14);
            $notification = \App\Models\Notification::create(['user_id' => $recipient->id, 'type' => $templateTypes[$i % count($templateTypes)], 'title' => 'Demo bildirim #'.$i, 'message' => 'Bildirim merkezi örnek mesajı #'.$i.'.', 'content' => 'Bildirim merkezi örnek mesajı #'.$i.'.', 'data' => ['demo' => true, 'sequence' => $i], 'channel' => ['panel', 'email', 'sms'][$i % 3], 'priority' => ['low', 'normal', 'high', 'urgent'][$i % 4], 'status' => $isRead ? 'Read' : 'Unread', 'read_at' => $isRead ? $createdAt->copy()->addHours(2) : null, 'sent_at' => $createdAt, 'created_at' => $createdAt, 'updated_at' => $createdAt]);
            \App\Models\NotificationLog::create(['notification_id' => $notification->id, 'recipient' => $recipient->email, 'channel' => $notification->channel, 'provider' => 'Demo', 'status' => 'Sent', 'sent_at' => $createdAt]);
        }

        foreach (['SendNotificationJob', 'GenerateReportJob', 'ProcessDocumentJob', 'ProcessPaymentReminderJob'] as $index => $jobName) {
            \App\Models\JobHistory::create(['job_name' => 'App\\Jobs\\'.$jobName, 'status' => $index === 3 ? 'failed' : 'completed', 'payload' => ['demo' => true], 'started_at' => now()->subMinutes($index + 10), 'completed_at' => now()->subMinutes($index + 9), 'error_message' => $index === 3 ? 'Demo provider timeout.' : null]);
        }
        foreach (['payment-reminders', 'upcoming-exams', 'attendance-warnings', 'pending-followups', 'weekly-system-report'] as $index => $automation) {
            \App\Models\AutomationLog::create(['job_name' => $automation, 'status' => $index === 2 ? 'failed' : 'completed', 'payload' => ['demo' => true], 'started_at' => now()->subHours($index + 1), 'completed_at' => now()->subHours($index + 1)->addMinutes(2), 'error_message' => $index === 2 ? 'Demo automation exception.' : null]);
        }

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

        // CRM Seeds
        $branch2 = Branch::create(['name' => 'Kadıköy Şubesi', 'slug' => 'kadikoy-subesi']);
        $branch3 = Branch::create(['name' => 'Beşiktaş Şubesi', 'slug' => 'besiktas-subesi']);

        $advisors = [];
        for ($i = 1; $i <= 5; $i++) {
            $advisors[] = \App\Models\User::create([
                'name' => "Danışman Personel {$i}",
                'email' => "advisor{$i}@dershane.com",
                'password' => bcrypt('password'),
            ]);
        }

        $srcWeb = \App\Models\LeadSource::create(['name' => 'Web Sitesi', 'code' => 'WEBSITE']);
        $srcIg = \App\Models\LeadSource::create(['name' => 'Instagram', 'code' => 'INSTAGRAM']);
        $srcGoogle = \App\Models\LeadSource::create(['name' => 'Google Arama', 'code' => 'GOOGLE']);
        $srcPhone = \App\Models\LeadSource::create(['name' => 'Telefon Arama', 'code' => 'PHONE']);

        $stNew = \App\Models\LeadStatus::create(['name' => 'Yeni Aday', 'code' => 'NEW_LEAD', 'color' => '#4F46E5', 'sort_order' => 1]);
        $stCalled = \App\Models\LeadStatus::create(['name' => 'Arandı', 'code' => 'CALLED', 'color' => '#10B981', 'sort_order' => 2]);
        $stContact = \App\Models\LeadStatus::create(['name' => 'Görüşme Yapıldı', 'code' => 'CONTACTED', 'color' => '#F59E0B', 'sort_order' => 3]);
        $stOffer = \App\Models\LeadStatus::create(['name' => 'Teklif Verildi', 'code' => 'OFFER_SENT', 'color' => '#3B82F6', 'sort_order' => 4]);
        $stReg = \App\Models\LeadStatus::create(['name' => 'Kayıt Oldu', 'code' => 'REGISTERED', 'color' => '#10B981', 'sort_order' => 5]);
        $stLost = \App\Models\LeadStatus::create(['name' => 'Kaybedildi', 'code' => 'LOST', 'color' => '#EF4444', 'sort_order' => 6]);

        $names = [
            ['Kaan', 'Arslan'], ['Esra', 'Kılıç'], ['Murat', 'Çelik'], ['Selin', 'Kaya'], ['Bora', 'Demir'],
            ['Ece', 'Yurt'], ['Can', 'Yılmaz'], ['İrem', 'Aydın'], ['Deniz', 'Koç'], ['Mert', 'Yıldız'],
            ['Derya', 'Güler'], ['Burak', 'Şahin'], ['Gözde', 'Öztürk'], ['Emre', 'Kartal'], ['Begüm', 'Bulut'],
            ['Yiğit', 'Karaca'], ['Ceren', 'Tekin'], ['Oğuz', 'Peker'], ['Melis', 'Yalçın'], ['Alp', 'Uyar']
        ];

        foreach ($names as $idx => $n) {
            $advisor = $advisors[$idx % 5];
            $branch = ($idx % 3 === 0) ? $branch : (($idx % 3 === 1) ? $branch2 : $branch3);
            $source = ($idx % 4 === 0) ? $srcWeb : (($idx % 4 === 1) ? $srcIg : (($idx % 4 === 2) ? $srcGoogle : $srcPhone));
            $status = ($idx % 6 === 0) ? $stNew : (($idx % 6 === 1) ? $stCalled : (($idx % 6 === 2) ? $stContact : (($idx % 6 === 3) ? $stOffer : (($idx % 6 === 4) ? $stReg : $stLost))));

            $lead = \App\Models\Lead::create([
                'first_name' => $n[0],
                'last_name' => $n[1],
                'phone' => '0555 000 00' . str_pad($idx, 2, '0', STR_PAD_LEFT),
                'whatsapp' => '0555 000 00' . str_pad($idx, 2, '0', STR_PAD_LEFT),
                'email' => strtolower($n[0]) . '@example.com',
                'school' => 'Anadolu Lisesi',
                'grade' => '12',
                'city' => 'İstanbul',
                'district' => 'Kadıköy',
                'program' => 'YKS Sayısal',
                'lead_source_id' => $source->id,
                'lead_status_id' => $status->id,
                'branch_id' => $branch->id,
                'advisor_id' => $advisor->id,
            ]);

            \App\Models\LeadActivity::create([
                'lead_id' => $lead->id,
                'action_type' => 'Created',
                'description' => 'Aday öğrenci sisteme kaydedildi.',
                'user_id' => $advisor->id,
            ]);

            if ($idx % 2 === 0) {
                \App\Models\LeadNote::create([
                    'lead_id' => $lead->id,
                    'user_id' => $advisor->id,
                    'note_text' => 'Veli kayıt şartlarını ve ödeme seçeneklerini sordu. TYT-AYT hazırlık programı ilgisini çekiyor.',
                ]);
            }

            if ($idx % 3 === 0) {
                \App\Models\LeadFollowup::create([
                    'lead_id' => $lead->id,
                    'user_id' => $advisor->id,
                    'followup_date' => now()->addDays(2),
                    'reminder_note' => 'Kayıt sözleşmesi gönderilip aranacak.',
                    'priority' => 'High',
                    'status' => 'Pending',
                ]);
            }
        }

        // Admission & Enrollment Seeds
        $contractTpl = \App\Models\ContractTemplate::create([
            'title' => 'Özel Öğretim Kursu Öğrenci Kayıt Sözleşmesi',
            'code' => 'KURS-SOZLESME-2026',
            'content' => "İşbu sözleşme {branch_name} ile {student_name} (T.C.: {tc_no}) arasında {date} tarihinde akdedilmiştir.\nProgram: {program}\nToplam Öğrenim Ücreti: {total_amount}\nÖdenen Kapora: {deposit_amount}\nÖğrenci kurum kurallarına uymayı, kurum ise eğitim hizmetini eksiksiz sunmayı taahhüt eder.",
            'is_active' => true,
        ]);

        // 5 Ön Kayıt (Pre-registrations)
        for ($i = 1; $i <= 5; $i++) {
            $adm = \App\Models\StudentAdmission::create([
                'admission_no' => 'ADM-2026-000' . $i,
                'first_name' => "ÖnkayıtOgrenci{$i}",
                'last_name' => "Kaya",
                'phone' => "0555 100 000{$i}",
                'tc_no' => "1000000000{$i}",
                'program' => "YKS Sayısal Hazırlık",
                'total_amount' => 45000.00,
                'deposit_amount' => 5000.00,
                'status' => ($i % 2 === 0) ? 'document_pending' : 'pre_registration',
                'branch_id' => $branch->id,
            ]);

            \App\Models\AdmissionDocument::create([
                'student_admission_id' => $adm->id,
                'document_type' => 'Kimlik',
                'file_name' => 'TC_Kimlik_Fotokopisi.pdf',
                'file_path' => 'documents/kimlik_' . $adm->id . '.pdf',
                'status' => ($i % 2 === 0) ? 'pending' : 'approved',
            ]);

            \App\Models\AdmissionStatusLog::create([
                'student_admission_id' => $adm->id,
                'from_status' => null,
                'to_status' => $adm->status,
                'description' => 'Aday ön kayıt başvurusu alındı.',
            ]);
        }

        // 5 Kesin Kayıt (Enrolled)
        for ($j = 1; $j <= 5; $j++) {
            $admEnr = \App\Models\StudentAdmission::create([
                'admission_no' => 'ADM-2026-010' . $j,
                'first_name' => "KesinKayitOgrenci{$j}",
                'last_name' => "Yılmaz",
                'phone' => "0555 200 000{$j}",
                'tc_no' => "2000000000{$j}",
                'program' => "TYT-AYT Yoğun Kamp",
                'total_amount' => 50000.00,
                'deposit_amount' => 10000.00,
                'status' => 'enrolled',
                'branch_id' => $branch->id,
            ]);

            $std = \App\Models\Student::create([
                'first_name' => $admEnr->first_name,
                'last_name' => $admEnr->last_name,
                'identity_number' => $admEnr->tc_no,
                'student_number' => 'STD-2026-010' . $j,
                'branch_id' => $branch->id,
                'status' => 'Active',
            ]);

            $inv = \App\Models\Invoice::create([
                'student_id' => $std->id,
                'invoice_number' => 'INV-202607-' . rand(1000, 9999),
                'issue_date' => now()->toDateString(),
                'due_date' => now()->addDays(30)->toDateString(),
                'total_amount' => 50000.00,
                'paid_amount' => 10000.00,
                'status' => 'Partial',
            ]);

            \App\Models\InvoiceItem::create([
                'invoice_id' => $inv->id,
                'description' => 'TYT-AYT Yoğun Kamp Yıllık Öğrenim Faturası',
                'quantity' => 1,
                'unit_price' => 50000.00,
                'total_price' => 50000.00,
            ]);

            \App\Models\Payment::create([
                'payment_number' => 'PAY-202607-00' . $j,
                'invoice_id' => $inv->id,
                'student_id' => $std->id,
                'amount' => 10000.00,
                'payment_date' => now(),
                'notes' => 'Kesin kayıt kapora ödemesi tahsilatı',
                'status' => 'Completed',
            ]);

            \App\Models\StudentEnrollment::create([
                'student_admission_id' => $admEnr->id,
                'student_id' => $std->id,
                'branch_id' => $branch->id,
                'invoice_id' => $inv->id,
                'enrollment_no' => 'ENR-2026-000' . $j,
                'enrollment_date' => now()->toDateString(),
                'academic_year' => '2026-2027',
                'final_fee' => 50000.00,
                'status' => 'completed',
            ]);

            \App\Models\EnrollmentContract::create([
                'student_admission_id' => $admEnr->id,
                'contract_template_id' => $contractTpl->id,
                'contract_no' => 'CNT-2026-00' . $j,
                'rendered_content' => "İşbu sözleşme {$branch->name} ile {$admEnr->first_name} {$admEnr->last_name} arasında akdedilmiştir. Yıllık Ücret: 50.000 TL.",
                'status' => 'signed',
                'signed_at' => now(),
            ]);

            \App\Models\AdmissionStatusLog::create([
                'student_admission_id' => $admEnr->id,
                'from_status' => 'payment_pending',
                'to_status' => 'enrolled',
                'description' => 'Kesin kayıt tamamlandı, öğrenci kartı ve faturası oluşturuldu.',
            ]);
        }

        // --- HR SEED DATA ---
        // 1. Departmanlar
        $dep1 = \App\Models\Department::create(['name' => 'Eğitim Departmanı', 'code' => 'EGT', 'description' => 'Eğitim-öğretim ve rehberlik süreçlerini yürüten departman.']);
        $dep2 = \App\Models\Department::create(['name' => 'Finans & Muhasebe', 'code' => 'FIN', 'description' => 'Tahsilat, ödeme, maaş ve fatura süreçlerini yöneten departman.']);
        $dep3 = \App\Models\Department::create(['name' => 'İdari İşler & İK', 'code' => 'ADM', 'description' => 'Kurum operasyonları, satın almalar ve personel işleri departmanı.']);

        // 2. Pozisyonlar
        $pos1 = \App\Models\Position::create(['department_id' => $dep1->id, 'name' => 'Kıdemli Eğitmen', 'level' => 'Senior', 'base_salary' => 45000.00, 'description' => 'Sınıf derslerini yürüten kıdemli öğretmen.']);
        $pos2 = \App\Models\Position::create(['department_id' => $dep2->id, 'name' => 'Muhasebe Uzmanı', 'level' => 'Mid', 'base_salary' => 38000.00, 'description' => 'Finansal kayıtları tutan uzman personel.']);
        $pos3 = \App\Models\Position::create(['department_id' => $dep3->id, 'name' => 'İK Yöneticisi', 'level' => 'Lead', 'base_salary' => 50000.00, 'description' => 'Personel ve insan kaynakları süreç sorumlusu.']);

        // 3. Personeller (15 Adet)
        $employees = [];
        $names = [
            ['Ahmet', 'Yılmaz'], ['Ayşe', 'Kaya'], ['Mehmet', 'Demir'], ['Fatma', 'Çelik'], ['Mustafa', 'Yıldız'],
            ['Emine', 'Şahin'], ['Ali', 'Öztürk'], ['Zeynep', 'Aydın'], ['Hüseyin', 'Arslan'], ['Esra', 'Polat'],
            ['İbrahim', 'Kılıç'], ['Elif', 'Koç'], ['Yusuf', 'Aslan'], ['Merve', 'Özdemir'], ['Murat', 'Tekin']
        ];

        for ($i = 0; $i < 15; $i++) {
            $dept = ($i % 3 === 0) ? $dep1 : (($i % 3 === 1) ? $dep2 : $dep3);
            $pos = ($i % 3 === 0) ? $pos1 : (($i % 3 === 1) ? $pos2 : $pos3);
            $emp = \App\Models\Employee::create([
                'employee_no' => 'EMP-' . (1000 + $i),
                'user_id' => $user->id, // demo user
                'department_id' => $dept->id,
                'position_id' => $pos->id,
                'first_name' => $names[$i][0],
                'last_name' => $names[$i][1],
                'tc_no' => '111111111' . str_pad((string)$i, 2, '0', STR_PAD_LEFT),
                'birth_date' => '1990-05-' . str_pad((string)($i + 1), 2, '0', STR_PAD_LEFT),
                'gender' => ($i % 2 === 0) ? 'Erkek' : 'Kadın',
                'phone' => '0555 300 00' . str_pad((string)$i, 2, '0', STR_PAD_LEFT),
                'email' => strtolower($names[$i][0]) . '.' . strtolower($names[$i][1]) . '@dershane.com',
                'address' => 'Örnek Mahallesi No: ' . $i,
                'hire_date' => '2024-01-15',
                'contract_type' => 'Full-time',
                'employment_status' => 'Active',
                'salary' => $pos->base_salary,
                'iban' => 'TR90 0006 2000 0000 1234 5678 ' . str_pad((string)$i, 2, '0', STR_PAD_LEFT),
                'emergency_contact' => 'Yakını ' . $i,
                'emergency_phone' => '0555 400 00' . str_pad((string)$i, 2, '0', STR_PAD_LEFT),
                'notes' => 'Örnek personel açıklaması.'
            ]);
            $employees[] = $emp;
        }

        // 4. İzin Türleri Seedi
        $lt1 = \App\Models\LeaveType::create(['name' => 'Yıllık İzin', 'code' => 'Annual', 'max_days' => 15]);
        $lt2 = \App\Models\LeaveType::create(['name' => 'Hastalık İzni', 'code' => 'Sick', 'max_days' => 5]);
        $lt3 = \App\Models\LeaveType::create(['name' => 'Doğum İzni', 'code' => 'Maternity', 'max_days' => 112]);
        $lt4 = \App\Models\LeaveType::create(['name' => 'Babalık İzni', 'code' => 'Paternity', 'max_days' => 5]);
        $lt5 = \App\Models\LeaveType::create(['name' => 'İdari İzin', 'code' => 'Administrative', 'max_days' => 3]);
        $lt6 = \App\Models\LeaveType::create(['name' => 'Ücretsiz İzin', 'code' => 'Unpaid', 'max_days' => 30]);

        // 5. 10 Adet İzin Talebi
        for ($i = 0; $i < 10; $i++) {
            \App\Models\LeaveRequest::create([
                'employee_id' => $employees[$i]->id,
                'leave_type_id' => ($i % 2 === 0) ? $lt1->id : $lt2->id,
                'start_date' => '2026-07-' . str_pad((string)($i + 5), 2, '0', STR_PAD_LEFT),
                'end_date' => '2026-07-' . str_pad((string)($i + 7), 2, '0', STR_PAD_LEFT),
                'days' => 3,
                'reason' => 'Dinlenme ve kişisel işler.',
                'status' => ($i % 3 === 0) ? 'Approved' : (($i % 3 === 1) ? 'Rejected' : 'Pending'),
                'approved_by' => $user->id,
                'approved_at' => now(),
            ]);
        }

        // 6. 8 Adet Bordro
        for ($i = 0; $i < 8; $i++) {
            $emp = $employees[$i];
            $gross = $emp->salary + 2000.00 + 1500.00;
            $insurance = round($gross * 0.14, 2);
            $tax = round(($gross - $insurance) * 0.15, 2);
            $net = $gross - $insurance - $tax - 200.00;

            \App\Models\Payroll::create([
                'employee_id' => $emp->id,
                'month' => 7,
                'year' => 2026,
                'gross_salary' => $gross,
                'bonus' => 2000.00,
                'overtime_amount' => 1500.00,
                'deductions' => 200.00,
                'tax' => $tax,
                'insurance' => $insurance,
                'net_salary' => $net,
                'status' => ($i % 2 === 0) ? 'Paid' : 'Approved',
                'payment_date' => ($i % 2 === 0) ? now()->toDateString() : null,
            ]);
        }

        // 7. 20 Adet Devamsızlık / Giriş-Çıkış
        for ($i = 0; $i < 20; $i++) {
            $emp = $employees[$i % 15];
            \App\Models\EmployeeAttendance::create([
                'employee_id' => $emp->id,
                'date' => '2026-07-' . str_pad((string)(10 + intval($i/2)), 2, '0', STR_PAD_LEFT),
                'check_in' => '09:15:00',
                'check_out' => '17:30:00',
                'worked_minutes' => 495,
                'overtime_minutes' => 15,
                'late_minutes' => 15,
            ]);
        }

        // 8. 10 Adet Masraf
        for ($i = 0; $i < 10; $i++) {
            \App\Models\Expense::create([
                'employee_id' => $employees[$i]->id,
                'title' => 'Ofis kırtasiye malzemeleri ve ulaşım.',
                'amount' => 150.00 * ($i + 1),
                'category' => 'Ofis Malzemesi',
                'receipt' => 'EXP-R-' . (5000 + $i),
                'status' => ($i % 2 === 0) ? 'Approved' : 'Pending',
            ]);
        }

        // 9. 5 Adet Avans
        for ($i = 0; $i < 5; $i++) {
            \App\Models\Advance::create([
                'employee_id' => $employees[$i]->id,
                'amount' => 1000.00 * ($i + 1),
                'reason' => 'Acil özel harcama ve fatura ödemesi.',
                'status' => ($i % 2 === 0) ? 'Approved' : 'Pending',
            ]);
        }

        // 10. 10 Adet Performans Değerlendirmesi
        for ($i = 0; $i < 10; $i++) {
            \App\Models\PerformanceReview::create([
                'employee_id' => $employees[$i]->id,
                'reviewer_id' => $user->id,
                'period' => '2026-Q2',
                'score' => 75 + $i,
                'strengths' => 'İş sorumluluğu yüksek, iş birliğine açık.',
                'weaknesses' => 'Zaman planlaması konusunda gelişmeli.',
                'comments' => 'Genel gidişatı son derece olumlu.'
            ]);
        }

        // --- INVENTORY & ASSET SEED DATA ---
        // 1. 5 Asset Categories
        $ac1 = \App\Models\AssetCategory::create(['name' => 'Bilgisayar & Çevre Birimleri', 'code' => 'ELK-COMP', 'description' => 'Laptoplar, masaüstü bilgisayarlar, monitörler.']);
        $ac2 = \App\Models\AssetCategory::create(['name' => 'Ofis & Sınıf Mobilyaları', 'code' => 'MOB-FURN', 'description' => 'Sandalyeler, masalar, dolaplar, sıralar.']);
        $ac3 = \App\Models\AssetCategory::create(['name' => 'Görüntüleme Sistemleri', 'code' => 'ELK-PROJ', 'description' => 'Projeksiyonlar, akıllı tahtalar.']);
        $ac4 = \App\Models\AssetCategory::create(['name' => 'İklimlendirme', 'code' => 'ELK-CLIM', 'description' => 'Klimalar ve ısıtıcı cihazlar.']);
        $ac5 = \App\Models\AssetCategory::create(['name' => 'Ses & Yayın Sistemleri', 'code' => 'ELK-SND', 'description' => 'Mikrofonlar, hoparlörler ve amfiler.']);

        // 2. 5 Inventory Categories
        $ic1 = \App\Models\InventoryCategory::create(['name' => 'Kırtasiye Sarf Malzemeleri', 'code' => 'KRT-STN', 'description' => 'Kalemler, dosyalar, klasörler.']);
        $ic2 = \App\Models\InventoryCategory::create(['name' => 'Temizlik Malzemeleri', 'code' => 'TEM-CLN', 'description' => 'Sabunlar, dezenfektanlar, peçeteler.']);
        $ic3 = \App\Models\InventoryCategory::create(['name' => 'Matbu Evraklar & Yayınlar', 'code' => 'MAT-DOC', 'description' => 'Sınav kitapçıkları, ders föyleri.']);
        $ic4 = \App\Models\InventoryCategory::create(['name' => 'İkramlık & Mutfak', 'code' => 'CAT-KIT', 'description' => 'Çay, kahve, şeker, bardaklar.']);
        $ic5 = \App\Models\InventoryCategory::create(['name' => 'Elektronik Sarf', 'code' => 'ELK-SRF', 'description' => 'Piller, kablolar, tonerler.']);

        // 3. 5 Depolar (Warehouses)
        $warehouses = [];
        for ($i = 1; $i <= 5; $i++) {
            $warehouses[] = \App\Models\Warehouse::create([
                'branch_id' => $branch->id,
                'name' => 'Depo Zone ' . $i,
                'description' => 'Bölüm ' . $i . ' için sarf malzeme depolama alanı.'
            ]);
        }

        // 4. Konumlar (Asset Locations)
        $al1 = \App\Models\AssetLocation::create(['branch_id' => $branch->id, 'name' => 'A Blok 101 Nolu Sınıf', 'description' => 'Eğitim sınıfları.']);
        $al2 = \App\Models\AssetLocation::create(['branch_id' => $branch->id, 'name' => 'Yönetim Ofisi', 'description' => 'İdarecilerin bulunduğu alan.']);
        $al3 = \App\Models\AssetLocation::create(['branch_id' => $branch->id, 'name' => 'Rehberlik Servisi', 'description' => 'Öğrenci danışma odası.']);
        $al4 = \App\Models\AssetLocation::create(['branch_id' => $branch->id, 'name' => 'Öğretmenler Odası', 'description' => 'Eğitmen dinlenme odası.']);
        $al5 = \App\Models\AssetLocation::create(['branch_id' => $branch->id, 'name' => 'Ana Depo', 'description' => 'Genel demirbaş depolama alanı.']);

        // 5. 30 Demirbaşlar (Assets)
        $assets = [];
        for ($i = 1; $i <= 30; $i++) {
            $cat = ($i % 5 === 1) ? $ac1 : (($i % 5 === 2) ? $ac2 : (($i % 5 === 3) ? $ac3 : (($i % 5 === 4) ? $ac4 : $ac5)));
            $loc = ($i % 5 === 1) ? $al1 : (($i % 5 === 2) ? $al2 : (($i % 5 === 3) ? $al3 : (($i % 5 === 4) ? $al4 : $al5)));
            $assets[] = \App\Models\Asset::create([
                'category_id' => $cat->id,
                'asset_code' => 'AST-' . (2000 + $i),
                'name' => $cat->name . ' Cihazı #' . $i,
                'brand' => 'Marka ' . $i,
                'model' => 'Model ' . $i,
                'serial_number' => 'SN-' . rand(100000, 999999),
                'purchase_date' => '2025-05-10',
                'purchase_price' => 500.00 * $i,
                'warranty_end_date' => '2027-05-10',
                'status' => ($i % 10 === 0) ? 'maintenance' : (($i % 12 === 0) ? 'broken' : 'active'),
                'location_id' => $loc->id,
                'description' => 'Demirbaş açıklama detayı ' . $i
            ]);
        }

        // 6. 20 Stok Ürünü (Inventory Items)
        $items = [];
        for ($i = 1; $i <= 20; $i++) {
            $cat = ($i % 5 === 1) ? $ic1 : (($i % 5 === 2) ? $ic2 : (($i % 5 === 3) ? $ic3 : (($i % 5 === 4) ? $ic4 : $ic5)));
            $wh = $warehouses[$i % 5];
            $items[] = \App\Models\InventoryItem::create([
                'category_id' => $cat->id,
                'warehouse_id' => $wh->id,
                'name' => $cat->name . ' Ürünü #' . $i,
                'sku' => 'SKU-' . (5000 + $i),
                'unit' => ($i % 3 === 0) ? 'Kutu' : 'Paket',
                'quantity' => 10 + ($i * 2),
                'minimum_quantity' => 5,
                'description' => 'Sarf malzeme ürün açıklaması ' . $i
            ]);
        }

        // 7. 50 Stok Hareketi (Inventory Transactions)
        for ($i = 1; $i <= 50; $i++) {
            $item = $items[$i % 20];
            \App\Models\InventoryTransaction::create([
                'item_id' => $item->id,
                'type' => ($i % 3 === 0) ? 'purchase' : 'usage',
                'quantity' => ($i % 3 === 0) ? 10 : 2,
                'reference_type' => 'Manual',
                'reference_id' => $i,
                'description' => 'Periyodik sarf/giriş işlemi #' . $i,
                'created_by' => $user->id
            ]);
        }

        // 8. 10 Tedarikçi (Suppliers)
        $suppliers = [];
        for ($i = 1; $i <= 10; $i++) {
            $suppliers[] = \App\Models\Supplier::create([
                'name' => 'Tedarikçi Firma #' . $i,
                'phone' => '0212 500 00 ' . str_pad((string)$i, 2, '0', STR_PAD_LEFT),
                'email' => 'info@tedarikci' . $i . '.com',
                'address' => 'Sanayi Mahallesi No: ' . $i,
                'tax_number' => 'TX-982374' . str_pad((string)$i, 2, '0', STR_PAD_LEFT)
            ]);
        }

        // 9. 5 Satın Alma (Purchase Orders)
        for ($i = 1; $i <= 5; $i++) {
            \App\Models\PurchaseOrder::create([
                'supplier_id' => $suppliers[$i - 1]->id,
                'order_number' => 'ORD-2026-000' . $i,
                'order_date' => '2026-07-' . str_pad((string)(10 + $i), 2, '0', STR_PAD_LEFT),
                'total_amount' => 5000.00 * $i,
                'status' => ($i % 2 === 0) ? 'completed' : 'approved',
                'notes' => 'Satın alma siparişi kalemleri #' . $i
            ]);
        }

        // 10. 10 Bakım Kaydı (Maintenance Records)
        for ($i = 1; $i <= 10; $i++) {
            \App\Models\MaintenanceRecord::create([
                'asset_id' => $assets[$i]->id,
                'employee_id' => $employees[$i % 15]->id,
                'maintenance_date' => '2026-07-' . str_pad((string)(5 + $i), 2, '0', STR_PAD_LEFT),
                'cost' => 150.00 * $i,
                'description' => 'Periyodik temizlik, filtre veya parça değişimi.',
                'status' => 'completed'
            ]);
        }

        // --- DOCUMENT MANAGEMENT SEED DATA ---
        // 1. 10 Document Categories
        $docCategories = [
            ['name' => 'Öğrenci Kayıt Belgeleri', 'slug' => 'ogrenci-kayit-belgeleri', 'color' => '#0d9488'],
            ['name' => 'Personel & Özlük Dosyaları', 'slug' => 'personel-ozluk-dosyalari', 'color' => '#2563eb'],
            ['name' => 'Öğretmen Belgeleri & Sertifikalar', 'slug' => 'ogretmen-belgeleri', 'color' => '#7c3aed'],
            ['name' => 'Hizmet & Kayıt Sözleşmeleri', 'slug' => 'hizmet-sozlesmeleri', 'color' => '#db2777'],
            ['name' => 'Mali Faturalar & Fişler', 'slug' => 'mali-faturalar', 'color' => '#ea580c'],
            ['name' => 'Banka Dekontları & Tahsilat', 'slug' => 'banka-dekontlari', 'color' => '#16a34a'],
            ['name' => 'Kurumsal Evraklar & Yönetmelik', 'slug' => 'kurumsal-evraklar', 'color' => '#475569'],
            ['name' => 'Sınav & Ders Föyleri Arşivi', 'slug' => 'sinav-ders-foyleri', 'color' => '#ca8a04'],
            ['name' => 'MEB İzin & Resmi Yazışmalar', 'slug' => 'meb-resmi-yazismalar', 'color' => '#dc2626'],
            ['name' => 'Genel Dijital Arşiv', 'slug' => 'genel-dijital-arsiv', 'color' => '#0891b2'],
        ];

        $createdDocCats = [];
        foreach ($docCategories as $c) {
            $createdDocCats[] = \App\Models\DocumentCategory::create($c);
        }

        // Fetch students for polymorphic linking
        $students = \App\Models\Student::limit(20)->get();

        // 2. 100 Document Records
        for ($i = 1; $i <= 100; $i++) {
            $cat = $createdDocCats[$i % 10];
            $ext = ($i % 4 === 0) ? 'pdf' : (($i % 4 === 1) ? 'docx' : (($i % 4 === 2) ? 'xlsx' : 'png'));
            
            $docableType = null;
            $docableId = null;
            if ($i % 3 === 0 && count($students) > 0) {
                $docableType = \App\Models\Student::class;
                $docableId = $students[$i % count($students)]->id;
            } elseif ($i % 5 === 0 && count($employees) > 0) {
                $docableType = \App\Models\Employee::class;
                $docableId = $employees[$i % count($employees)]->id;
            }

            $doc = \App\Models\Document::create([
                'documentable_type' => $docableType,
                'documentable_id' => $docableId,
                'category_id' => $cat->id,
                'title' => $cat->name . ' - Doküman #' . $i,
                'file_name' => 'evrak_' . $i . '.' . $ext,
                'file_path' => 'storage/documents/evrak_' . $i . '.' . $ext,
                'type' => $ext,
                'file_type' => $ext,
                'file_size' => 102400 * rand(1, 50),
                'uploaded_by' => $user->id,
                'status' => 'active',
                'description' => 'Sistem tarafından otomatik oluşturulmuş dijital arşiv evrak örneği #' . $i,
                'created_at' => now()->subDays(rand(1, 180)),
            ]);

            // Version 1
            \App\Models\DocumentVersion::create([
                'document_id' => $doc->id,
                'version_number' => 1,
                'file_path' => $doc->file_path,
                'uploaded_by' => $user->id,
                'notes' => 'İlk yükleme versiyonu.',
                'created_at' => $doc->created_at,
            ]);

            // Optional Version 2 for some
            if ($i % 5 === 0) {
                \App\Models\DocumentVersion::create([
                    'document_id' => $doc->id,
                    'version_number' => 2,
                    'file_path' => 'storage/documents/v2_evrak_' . $i . '.' . $ext,
                    'uploaded_by' => $user->id,
                    'notes' => 'İmzalı nüsha güncellendi.',
                    'created_at' => $doc->created_at->addDays(2),
                ]);
            }
        }
    }
}
