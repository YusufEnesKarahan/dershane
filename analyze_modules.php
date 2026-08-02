<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$lifecycles = [
    'Student' => [
        'Lead' => 'Lead',
        'Başvuru' => 'StudentAdmission',
        'Kayıt' => 'StudentEnrollment',
        'Sınıfa Atama' => 'Student', // Has class_id?
        'Ders Programı' => 'ClassSchedule',
        'Yoklama' => 'Attendance',
        'Ödev' => 'Assignment',
        'Sınav' => 'Exam',
        'Ödeme Planı' => 'PaymentPlan',
        'Tahsilat' => 'Payment',
        'Veli Portalı' => 'ParentStudent',
        'Mezuniyet' => 'StudentStatusHistory',
    ],
    'Teacher' => [
        'Başvuru' => 'TeacherProfile', // maybe?
        'İşe Alım' => 'TeacherContract',
        'Branş' => 'TeacherSubject',
        'Ders Ataması' => 'TeacherAssignment',
        'Program' => 'TeacherSchedule',
        'Yoklama' => 'EmployeeAttendance',
        'Performans' => 'TeacherPerformance',
        'İzin' => 'LeaveRequest',
        'Maaş' => 'Payroll',
    ],
    'Class' => [
        'Kurs' => 'Course',
        'Şube' => 'Branch',
        'Sınıf' => 'Classroom',
        'Kontenjan' => 'ClassroomCapacity',
        'Öğrenci' => 'Student',
        'Program' => 'ClassSchedule',
        'Yoklama' => 'AttendanceSession',
        'Sınav' => 'ExamSession',
    ],
    'Finance' => [
        'Kayıt' => 'RegistrationPayment',
        'Ödeme Planı' => 'PaymentPlan',
        'Taksit' => 'Invoice',
        'Tahsilat' => 'Payment',
        'İade' => 'Refund',
        'İndirim' => 'Discount',
        'Burs' => 'Scholarship',
        'Rapor' => 'ReportExport',
    ]
];

$components = ['Model', 'Migration', 'Controller', 'Service', 'Repository', 'DTO', 'Policy', 'Request (Validation)', 'Route', 'Blade', 'Event', 'Listener', 'Notification', 'Test'];

function checkExists($type, $name) {
    if ($type === 'Model') return file_exists(app_path("Models/{$name}.php"));
    if ($type === 'Controller') {
        // could be admin, api, etc.
        return file_exists(app_path("Http/Controllers/Admin/{$name}Controller.php")) || file_exists(app_path("Http/Controllers/{$name}Controller.php"));
    }
    if ($type === 'Service') return file_exists(app_path("Services/{$name}Service.php")) || file_exists(app_path("Domain/Platform/Services/{$name}Service.php")) || file_exists(app_path("Domain/Education/Services/{$name}Service.php"));
    if ($type === 'Repository') return file_exists(app_path("Repositories/{$name}Repository.php"));
    if ($type === 'DTO') return file_exists(app_path("DTOs/{$name}DTO.php"));
    if ($type === 'Policy') return file_exists(app_path("Policies/{$name}Policy.php"));
    if ($type === 'Request (Validation)') {
        return file_exists(app_path("Http/Requests/Store{$name}Request.php")) || file_exists(app_path("Http/Requests/{$name}Request.php"));
    }
    if ($type === 'Route') {
        // simplistic check: grep web.php or admin.php
        $adminRoutes = file_get_contents(base_path('routes/admin.php'));
        return stripos($adminRoutes, "{$name}Controller") !== false;
    }
    if ($type === 'Blade') {
        // e.g. resources/views/admin/students/index.blade.php
        $folder = strtolower(Str::plural($name));
        $folder2 = strtolower($name);
        return is_dir(resource_path("views/admin/{$folder}")) || is_dir(resource_path("views/admin/{$folder2}"));
    }
    if ($type === 'Migration') {
        $files = glob(database_path("migrations/*_create_*" . strtolower(Str::plural($name)) . "_table.php"));
        if (empty($files)) {
            // Also check snake_case
            $snake = Str::snake(Str::plural($name));
            $files = glob(database_path("migrations/*_create_{$snake}_table.php"));
            if(empty($files)) {
                // some tables might be grouped
                return false; 
            }
        }
        return true;
    }
    if ($type === 'Event') return file_exists(app_path("Events/{$name}Created.php"));
    if ($type === 'Listener') return file_exists(app_path("Listeners/Send{$name}Notification.php"));
    if ($type === 'Notification') return file_exists(app_path("Notifications/{$name}CreatedNotification.php"));
    if ($type === 'Test') return file_exists(base_path("tests/Feature/{$name}Test.php")) || file_exists(base_path("tests/Feature/Admin/{$name}ControllerTest.php"));
    
    return false;
}

$report = [];

foreach ($lifecycles as $lifecycle => $steps) {
    echo "=== $lifecycle Lifecycle ===\n";
    foreach ($steps as $step => $entityName) {
        echo "  - $step ($entityName)\n";
        foreach ($components as $comp) {
            $exists = checkExists($comp, $entityName);
            echo "    " . ($exists ? "✓" : "❌") . " $comp\n";
        }
        echo "\n";
    }
}
