<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\UserProfileController;
use App\Http\Controllers\Admin\UserPreferenceController;
use App\Http\Controllers\Admin\ExecutiveDashboardController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\LeadController;
use App\Http\Controllers\Admin\LeadPipelineController;
use App\Http\Controllers\Admin\LeadAnalyticsController;
use App\Http\Controllers\Admin\LeadDashboardController;
use App\Http\Controllers\Admin\AdmissionController;
use App\Http\Controllers\Admin\EnrollmentController;
use App\Http\Controllers\Admin\ContractController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\PageController;
use App\Http\Controllers\Admin\MediaController;
use App\Http\Controllers\Admin\MediaFolderController;
use App\Http\Controllers\Admin\PlatformSettingController;
use App\Http\Controllers\Admin\BlogController;
use App\Http\Controllers\Admin\BlogCategoryController;
use App\Http\Controllers\Admin\BlogTagController;
use App\Http\Controllers\Admin\BlogCommentController;
use App\Http\Controllers\Admin\TeacherController;
use App\Http\Controllers\Admin\TeacherScheduleController;
use App\Http\Controllers\Admin\TeacherPerformanceController;
use App\Http\Controllers\Admin\TeacherSalaryController;
use App\Http\Controllers\Admin\TeacherContractController;
use App\Http\Controllers\Admin\CourseController;
use App\Http\Controllers\Admin\CourseLevelController;
use App\Http\Controllers\Admin\ClassroomController;
use App\Http\Controllers\Admin\AcademicCalendarController;
use App\Http\Controllers\Admin\ClassScheduleController;
use App\Http\Controllers\Admin\HolidayController;
use App\Http\Controllers\Admin\StudentController;
use App\Http\Controllers\Admin\StudentEnrollmentController;
use App\Http\Controllers\Admin\AttendanceSessionController;
use App\Http\Controllers\Admin\AttendanceController;
use App\Http\Controllers\Admin\ExamController;
use App\Http\Controllers\Admin\ExamResultController;
use App\Http\Controllers\Admin\AssignmentController;
use App\Http\Controllers\Admin\AssignmentSubmissionController;
use App\Http\Controllers\Admin\InvoiceController;
use App\Http\Controllers\Admin\PaymentController;
use App\Http\Controllers\Admin\NotificationController;
use App\Http\Controllers\Admin\AnnouncementController;

// Admin Framework Routes
Route::middleware(['auth'])->prefix('admin')->name('admin.')->group(function () {
    
    // Dashboard is handled in auth.php right now, but let's keep all placeholder routes here
    // Route::get('dashboard', [ProfileController::class, 'dashboard'])->name('dashboard');

    // Access Management
    Route::post('users/bulk', [UserController::class, 'bulk'])->name('users.bulk');
    Route::post('users/{id}/restore', [UserController::class, 'restore'])->name('users.restore');
    Route::resource('users', UserController::class);
    
    Route::get('profile', [UserProfileController::class, 'edit'])->name('profile.edit');
    Route::post('profile', [UserProfileController::class, 'update'])->name('profile.update');
    Route::post('profile/password', [UserProfileController::class, 'password'])->name('profile.password');
    Route::post('profile/avatar', [UserProfileController::class, 'avatar'])->name('profile.avatar');
    Route::post('preferences', [UserPreferenceController::class, 'update'])->name('preferences.update');

    Route::post('roles/bulk', [RoleController::class, 'bulk'])->name('roles.bulk');
    Route::post('roles/{id}/restore', [RoleController::class, 'restore'])->name('roles.restore');
    Route::get('roles/{role}/clone', [RoleController::class, 'showClone'])->name('roles.showClone');
    Route::post('roles/{role}/clone', [RoleController::class, 'clone'])->name('roles.clone');
    Route::resource('roles', RoleController::class);
    Route::get('permissions', fn() => 'Permissions Placeholder')->name('permissions.index');
    
    // CMS
    Route::post('pages/bulk', [PageController::class, 'bulk'])->name('pages.bulk');
    Route::post('pages/{id}/restore', [PageController::class, 'restore'])->name('pages.restore');
    Route::post('pages/{page}/publish', [PageController::class, 'publish'])->name('pages.publish');
    Route::post('pages/{page}/duplicate', [PageController::class, 'duplicate'])->name('pages.duplicate');
    Route::get('pages/{page}/preview', [PageController::class, 'preview'])->name('pages.preview');
    Route::resource('pages', PageController::class);
    // Blog Suite
    Route::get('blogs/analytics', [BlogController::class, 'analytics'])->name('blogs.analytics');
    Route::post('blogs/{blog}/publish', [BlogController::class, 'publish'])->name('blogs.publish');
    Route::post('blogs/{blog}/archive', [BlogController::class, 'archive'])->name('blogs.archive');
    Route::post('blogs/{blog}/duplicate', [BlogController::class, 'duplicate'])->name('blogs.duplicate');
    Route::post('blogs/{blog}/revisions/{revision}/restore', [BlogController::class, 'restoreRevision'])->name('blogs.revisions.restore');
    Route::resource('blogs', BlogController::class);

    // Blog Categories & Tags
    Route::resource('categories', BlogCategoryController::class)->only(['index', 'store', 'destroy']);
    Route::post('tags/merge', [BlogTagController::class, 'merge'])->name('tags.merge');
    Route::resource('tags', BlogTagController::class)->only(['index', 'store', 'destroy']);

    // Comments Moderation
    Route::post('comments/{comment}/approve', [BlogCommentController::class, 'approve'])->name('comments.approve');
    Route::post('comments/{comment}/reject', [BlogCommentController::class, 'reject'])->name('comments.reject');
    Route::post('comments/{comment}/spam', [BlogCommentController::class, 'spam'])->name('comments.spam');
    Route::resource('comments', BlogCommentController::class)->only(['index', 'destroy']);

    Route::get('announcements', fn() => 'Announcements Placeholder')->name('announcements.index');

    // Education
    Route::get('students/analytics', [StudentController::class, 'analytics'])->name('students.analytics');
    Route::post('students/{student}/transfer', [StudentController::class, 'transfer'])->name('students.transfer');
    Route::post('students/enrollment', [StudentEnrollmentController::class, 'store'])->name('students.enrollment.store');
    Route::resource('students', StudentController::class);
    Route::get('attendances/analytics', [AttendanceController::class, 'analytics'])->name('attendances.analytics');
    Route::get('attendances/sessions/{session}/take', [AttendanceController::class, 'take'])->name('attendances.sessions.take');
    Route::post('attendances/sessions/{session}/take', [AttendanceController::class, 'storeBulk'])->name('attendances.sessions.store-bulk');
    Route::resource('attendances/sessions', AttendanceSessionController::class)->names('attendances.sessions');
    Route::get('exams/analytics', [ExamResultController::class, 'analytics'])->name('exams.analytics');
    Route::get('exams/{exam}/results', [ExamResultController::class, 'index'])->name('exams.results.index');
    Route::post('exams/{exam}/results', [ExamResultController::class, 'store'])->name('exams.results.store');
    Route::resource('exams', ExamController::class);
    Route::get('assignments/analytics', [AssignmentSubmissionController::class, 'analytics'])->name('assignments.analytics');
    Route::get('assignments/{assignment}/submissions', [AssignmentSubmissionController::class, 'index'])->name('assignments.submissions.index');
    Route::post('assignments/{assignment}/submissions', [AssignmentSubmissionController::class, 'store'])->name('assignments.submissions.store');
    Route::post('assignments/{assignment}/evaluate', [AssignmentSubmissionController::class, 'evaluate'])->name('assignments.submissions.evaluate');
    Route::resource('assignments', AssignmentController::class);
    Route::get('invoices/dashboard', [InvoiceController::class, 'dashboard'])->name('invoices.dashboard');
    Route::post('invoices/{invoice}/cancel', [InvoiceController::class, 'cancel'])->name('invoices.cancel');
    Route::resource('invoices', InvoiceController::class);
    Route::post('payments', [PaymentController::class, 'store'])->name('payments.store');
    Route::get('notifications/templates', [NotificationController::class, 'templates'])->name('notifications.templates');
    Route::get('notifications/analytics', [NotificationController::class, 'analytics'])->name('notifications.analytics');
    Route::resource('notifications', NotificationController::class);
    Route::resource('announcements', AnnouncementController::class);
    Route::get('teachers/{teacher}/performance', [TeacherPerformanceController::class, 'show'])->name('teachers.performance');
    Route::post('teachers/performance', [TeacherPerformanceController::class, 'store'])->name('teachers.performance.store');
    Route::get('teachers/analytics', [TeacherController::class, 'analytics'])->name('teachers.analytics');
    Route::resource('teachers', TeacherController::class);
    Route::resource('teachers-schedules', TeacherScheduleController::class)->only(['index', 'store'])->names([
        'index' => 'teachers.schedules.index',
        'store' => 'teachers.schedules.store',
    ]);
    Route::resource('teachers-performance', TeacherPerformanceController::class)->only(['index', 'store'])->names([
        'index' => 'teachers.performance.index',
        'store' => 'teachers.performance.store',
    ]);
    Route::resource('teachers-salary', TeacherSalaryController::class)->only(['index', 'store'])->names([
        'index' => 'teachers.salary.index',
        'store' => 'teachers.salary.store',
    ]);
    Route::resource('teachers-contracts', TeacherContractController::class)->only(['index', 'store'])->names([
        'index' => 'teachers.contracts.index',
        'store' => 'teachers.contracts.store',
    ]);
    Route::get('courses/analytics', [CourseController::class, 'analytics'])->name('courses.analytics');
    Route::resource('courses-levels', CourseLevelController::class)->only(['index', 'store', 'destroy'])->names([
        'index' => 'courses.levels.index',
        'store' => 'courses.levels.store',
        'destroy' => 'courses.levels.destroy',
    ]);
    Route::resource('courses', CourseController::class);
    Route::get('classrooms/analytics', [ClassroomController::class, 'analytics'])->name('classrooms.analytics');
    Route::get('classrooms/academic-calendar', [AcademicCalendarController::class, 'index'])->name('classrooms.academic-calendar.index');
    Route::post('classrooms/academic-calendar', [AcademicCalendarController::class, 'store'])->name('classrooms.academic-calendar.store');
    Route::get('classrooms/schedules', [ClassScheduleController::class, 'index'])->name('classrooms.schedules.index');
    Route::post('classrooms/schedules', [ClassScheduleController::class, 'store'])->name('classrooms.schedules.store');
    Route::get('classrooms/holidays', [HolidayController::class, 'index'])->name('classrooms.holidays.index');
    Route::post('classrooms/holidays', [HolidayController::class, 'store'])->name('classrooms.holidays.store');
    Route::resource('classrooms', ClassroomController::class);

    // Media Library
    Route::get('media-picker', [MediaController::class, 'pickerList'])->name('media.picker-list');
    Route::get('media/{media}/download', [MediaController::class, 'download'])->name('media.download');
    Route::post('media-folders', [MediaFolderController::class, 'store'])->name('media-folders.store');
    Route::delete('media-folders/{folder}', [MediaFolderController::class, 'destroy'])->name('media-folders.destroy');
    Route::resource('media', MediaController::class);

    // CRM
    Route::get('leads', fn() => 'Leads Placeholder')->name('leads.index');
    Route::get('contacts', fn() => 'Contacts Placeholder')->name('contacts.index');

    // System
    Route::get('branches', fn() => 'Branches Placeholder')->name('branches.index');
    Route::get('settings', [PlatformSettingController::class, 'index'])->name('settings.index');
    Route::post('settings', [PlatformSettingController::class, 'update'])->name('settings.update');
    Route::post('settings/test-mail', [PlatformSettingController::class, 'testMail'])->name('settings.test-mail');
    Route::post('settings/test-storage', [PlatformSettingController::class, 'testStorage'])->name('settings.test-storage');
    Route::get('settings/export', [PlatformSettingController::class, 'export'])->name('settings.export');
    Route::post('settings/import', [PlatformSettingController::class, 'import'])->name('settings.import');
    Route::post('settings/reset', [PlatformSettingController::class, 'reset'])->name('settings.reset');
    Route::get('logs', fn() => 'Logs Placeholder')->name('logs.index');

    // Reporting & BI routes
    Route::get('reporting/dashboard', [ExecutiveDashboardController::class, 'index'])->name('reporting.dashboard');
    Route::get('reporting/analytics', [ExecutiveDashboardController::class, 'analytics'])->name('reporting.analytics');
    Route::post('reporting/snapshot', [ExecutiveDashboardController::class, 'snapshot'])->name('reporting.snapshot');
    Route::get('reporting/reports', [ReportController::class, 'index'])->name('reporting.reports');
    Route::post('reporting/reports/schedules', [ReportController::class, 'storeSchedule'])->name('reporting.schedules.store');
    Route::post('reporting/reports/export', [ReportController::class, 'export'])->name('reporting.export');
    Route::get('reporting/reports/{id}/download', [ReportController::class, 'download'])->name('reporting.download');

    // CRM Routes
    Route::get('crm/dashboard', [LeadDashboardController::class, 'index'])->name('crm.dashboard');
    Route::get('crm/pipeline', [LeadPipelineController::class, 'index'])->name('crm.pipeline');
    Route::post('crm/pipeline/move', [LeadPipelineController::class, 'move'])->name('crm.pipeline.move');
    Route::post('crm/pipeline/{id}/convert', [LeadPipelineController::class, 'convert'])->name('crm.pipeline.convert');
    Route::post('crm/pipeline/{id}/close', [LeadPipelineController::class, 'close'])->name('crm.pipeline.close');
    Route::get('crm/analytics', [LeadAnalyticsController::class, 'index'])->name('crm.analytics');
    Route::get('crm/followups', [LeadDashboardController::class, 'followups'])->name('crm.followups');
    Route::post('crm/followups', [LeadDashboardController::class, 'storeFollowup'])->name('crm.followups.store');
    Route::post('crm/followups/{id}/complete', [LeadDashboardController::class, 'completeFollowup'])->name('crm.followups.complete');

    // Leads CRUD
    Route::post('leads/{id}/note', [LeadController::class, 'storeNote'])->name('leads.note.store');
    Route::post('leads/{id}/assign', [LeadController::class, 'assign'])->name('leads.assign');
    Route::resource('leads', LeadController::class);

    // Admission & Enrollment Routes
    Route::get('admission/dashboard', [AdmissionController::class, 'dashboard'])->name('admission.dashboard');
    Route::get('admission/workflow', [AdmissionController::class, 'workflow'])->name('admission.workflow');
    Route::get('admission/documents', [AdmissionController::class, 'documents'])->name('admission.documents');
    Route::post('admission/convert/{leadId}', [AdmissionController::class, 'convertLead'])->name('admission.convert');
    Route::post('admission/{id}/status', [AdmissionController::class, 'updateStatus'])->name('admission.status.update');
    Route::post('admission/{id}/note', [AdmissionController::class, 'storeNote'])->name('admission.note.store');
    Route::resource('admission', AdmissionController::class);

    Route::post('enrollment/complete', [EnrollmentController::class, 'complete'])->name('enrollment.complete');
    Route::post('enrollment/document/upload', [EnrollmentController::class, 'uploadDocument'])->name('enrollment.document.upload');
    Route::post('enrollment/document/{id}/approve', [EnrollmentController::class, 'approveDocument'])->name('enrollment.document.approve');

    Route::get('contracts', [ContractController::class, 'index'])->name('contracts.index');
    Route::post('contracts/generate', [ContractController::class, 'generate'])->name('contracts.generate');
    Route::post('contracts/{id}/sign', [ContractController::class, 'sign'])->name('contracts.sign');
});
