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
use App\Http\Controllers\Admin\DepartmentController;
use App\Http\Controllers\Admin\EmployeeController;
use App\Http\Controllers\Admin\PayrollController;
use App\Http\Controllers\Admin\LeaveController;
use App\Http\Controllers\Admin\EmployeeAttendanceController;
use App\Http\Controllers\Admin\ExpenseController;
use App\Http\Controllers\Admin\AdvanceController;
use App\Http\Controllers\Admin\PerformanceController;
use App\Http\Controllers\Admin\AnalyticsController as HRAnalyticsController;
use App\Http\Controllers\Admin\LessonScheduleController;
use App\Http\Controllers\Admin\HomeworkController;
use App\Http\Controllers\Admin\HomeworkSubmissionController;
use App\Http\Controllers\Admin\FinanceController;
use App\Http\Controllers\Admin\InstallmentController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\AssetController;
use App\Http\Controllers\Admin\AssetCategoryController;
use App\Http\Controllers\Admin\InventoryController;
use App\Http\Controllers\Admin\SupplierController;
use App\Http\Controllers\Admin\PurchaseController;
use App\Http\Controllers\Admin\MaintenanceController;
use App\Http\Controllers\Admin\TransferController;
use App\Http\Controllers\Admin\InventoryAnalyticsController as InvAnalyticsController;
use App\Http\Controllers\Admin\DocumentController;
use App\Http\Controllers\Admin\DocumentCategoryController;
use App\Http\Controllers\Admin\DocumentSearchController;
use App\Http\Controllers\Admin\PageController;
use App\Http\Controllers\Admin\MediaController;
use App\Http\Controllers\Admin\MediaFolderController;
use App\Http\Controllers\Admin\PlatformSettingController;
use App\Http\Controllers\Admin\LicenseController;
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
use App\Http\Controllers\Admin\NotificationTemplateController;
use App\Http\Controllers\Admin\NotificationAnalyticsController;
use App\Http\Controllers\Admin\SystemJobController;
use App\Http\Controllers\Admin\AnnouncementController;
use App\Http\Controllers\Admin\SaaSTenantController;
use App\Http\Controllers\Admin\SaaSHealthController;
use App\Http\Controllers\Admin\SubscriptionController;

// Admin Framework Routes
Route::middleware(['auth'])->prefix('admin')->name('admin.')->group(function () {
    
    // Onboarding Routes
    Route::get('/onboarding', [\App\Http\Controllers\Admin\OnboardingController::class, 'index'])->name('onboarding.index');
    Route::post('/onboarding/identity', [\App\Http\Controllers\Admin\OnboardingController::class, 'storeIdentity'])->name('onboarding.identity');
    Route::post('/onboarding/term', [\App\Http\Controllers\Admin\OnboardingController::class, 'storeTerm'])->name('onboarding.term');

    // Branch Switch
    Route::post('/branch/switch', function (\Illuminate\Http\Request $request) {
        $request->validate(['branch_id' => 'required|exists:branches,id']);
        // Verify user belongs to this branch if not Super Admin
        if (!auth()->user()->hasRole('Super Admin') && auth()->user()->branch_id != $request->branch_id) {
            abort(403);
        }
        $branch = \App\Models\Branch::find($request->branch_id);
        session(['active_branch_id' => $branch->id, 'active_branch_name' => $branch->name]);
        return back();
    })->name('branch.switch');

    // Profiles & Preferences (no specific permission required besides 'auth')
    Route::get('profile', [UserProfileController::class, 'edit'])->name('profile.edit');
    Route::post('profile', [UserProfileController::class, 'update'])->name('profile.update');
    Route::post('profile/password', [UserProfileController::class, 'password'])->name('profile.password');
    Route::post('profile/avatar', [UserProfileController::class, 'avatar'])->name('profile.avatar');
    Route::post('preferences', [UserPreferenceController::class, 'update'])->name('preferences.update');

    // Access Management - Users
    Route::middleware(['permission:users.view'])->group(function () {
        Route::post('users/bulk', [UserController::class, 'bulk'])->name('users.bulk');
        Route::post('users/{id}/restore', [UserController::class, 'restore'])->name('users.restore');
        Route::resource('users', UserController::class)->except(['show']);
    });

    // Access Management - Roles
    Route::middleware(['permission:roles.view'])->group(function () {
        Route::post('roles/bulk', [RoleController::class, 'bulk'])->name('roles.bulk');
        Route::post('roles/{id}/restore', [RoleController::class, 'restore'])->name('roles.restore');
        Route::get('roles/{role}/clone', [RoleController::class, 'showClone'])->name('roles.showClone');
        Route::post('roles/{role}/clone', [RoleController::class, 'clone'])->name('roles.clone');
        Route::resource('roles', RoleController::class);
    });
    
    // CMS Pages
    Route::middleware(['permission:pages.view'])->group(function () {
        Route::post('pages/bulk', [PageController::class, 'bulk'])->name('pages.bulk');
        Route::post('pages/{id}/restore', [PageController::class, 'restore'])->name('pages.restore');
        Route::post('pages/{page}/publish', [PageController::class, 'publish'])->name('pages.publish');
        Route::post('pages/{page}/duplicate', [PageController::class, 'duplicate'])->name('pages.duplicate');
        Route::get('pages/{page}/preview', [PageController::class, 'preview'])->name('pages.preview');
        Route::resource('pages', PageController::class)->except(['show']);
    });

    // CMS Blogs
    Route::middleware(['permission:blogs.view'])->group(function () {
        Route::get('blogs/analytics', [BlogController::class, 'analytics'])->name('blogs.analytics');
        Route::post('blogs/{blog}/publish', [BlogController::class, 'publish'])->name('blogs.publish');
        Route::post('blogs/{blog}/archive', [BlogController::class, 'archive'])->name('blogs.archive');
        Route::post('blogs/{blog}/duplicate', [BlogController::class, 'duplicate'])->name('blogs.duplicate');
        Route::post('blogs/{blog}/revisions/{revision}/restore', [BlogController::class, 'restoreRevision'])->name('blogs.revisions.restore');
        Route::resource('blogs', BlogController::class)->except(['show']);

        Route::resource('blog/categories', BlogCategoryController::class)->only(['index', 'store', 'destroy'])->names('blog-categories');
        Route::post('tags/merge', [BlogTagController::class, 'merge'])->name('tags.merge');
        Route::resource('tags', BlogTagController::class)->only(['index', 'store', 'destroy']);

        Route::post('comments/{comment}/approve', [BlogCommentController::class, 'approve'])->name('comments.approve');
        Route::post('comments/{comment}/reject', [BlogCommentController::class, 'reject'])->name('comments.reject');
        Route::post('comments/{comment}/spam', [BlogCommentController::class, 'spam'])->name('comments.spam');
        Route::resource('comments', BlogCommentController::class)->only(['index', 'destroy']);
    });

    // CMS Media Library
    Route::middleware(['permission:media.view'])->group(function () {
        Route::get('media-picker', [MediaController::class, 'pickerList'])->name('media.picker-list');
        Route::get('media/{media}/download', [MediaController::class, 'download'])->name('media.download');
        Route::post('media-folders', [MediaFolderController::class, 'store'])->name('media-folders.store');
        Route::delete('media-folders/{folder}', [MediaFolderController::class, 'destroy'])->name('media-folders.destroy');
        Route::resource('media', MediaController::class)->only(['index', 'store', 'destroy']);
    });

    // Notifications & Templates
    Route::middleware(['permission:notifications.view'])->group(function () {
        Route::get('notifications/dashboard', [NotificationController::class, 'dashboard'])->name('notifications.dashboard');
        Route::get('notifications/preferences', [NotificationController::class, 'preferences'])->name('notifications.preferences');
        Route::put('notifications/preferences', [NotificationController::class, 'updatePreferences'])->name('notifications.preferences.update');
        Route::get('notifications/templates', [NotificationTemplateController::class, 'index'])->name('notifications.templates');
        Route::post('notifications/templates', [NotificationTemplateController::class, 'store'])->name('notifications.templates.store');
        Route::get('notifications/analytics', NotificationAnalyticsController::class)->name('notifications.analytics');
        Route::post('notifications/{notification}/read', [NotificationController::class, 'markRead'])->name('notifications.read');
        Route::get('notifications', [NotificationController::class, 'index'])->name('notifications.index');
        Route::post('notifications', [NotificationController::class, 'store'])
            ->middleware('subscription.feature:sms')
            ->name('notifications.store');
    });

    // System Jobs
    Route::middleware(['permission:system.jobs.manage'])->group(function () {
        Route::get('system/jobs', [SystemJobController::class, 'dashboard'])->name('system.jobs.dashboard');
        Route::get('system/jobs/failed', [SystemJobController::class, 'failed'])->name('system.jobs.failed');
        Route::get('system/jobs/history', [SystemJobController::class, 'history'])->name('system.jobs.history');
        Route::get('system/automation-logs', [SystemJobController::class, 'automation'])->name('system.jobs.automation');
    });

    // Education - Students
    Route::middleware(['permission:students.view'])->group(function () {
        Route::get('students/analytics', [StudentController::class, 'analytics'])->name('students.analytics');
        Route::resource('students', StudentController::class);
    });
        
    // Exams
    Route::get('exams/{exam}/analysis', [ExamController::class, 'analysis'])->name('exams.analysis');
    Route::get('exams/{exam}/results', [\App\Http\Controllers\Admin\ExamResultController::class, 'index'])->name('exams.results');
    Route::post('exams/{exam}/results', [\App\Http\Controllers\Admin\ExamResultController::class, 'store'])->name('exams.results.store');
    Route::resource('exams', ExamController::class);

    // Finance (Sprint 9.1)
    Route::post('finance/{plan}/discount', [FinanceController::class, 'applyDiscount'])->name('finance.discount');
    Route::resource('finance', FinanceController::class);
    
    Route::post('installments/{installment}/collect', [InstallmentController::class, 'collect'])->name('installments.collect');
    Route::get('installments', [InstallmentController::class, 'index'])->name('installments.index');
    
    Route::post('payments/{payment}/refund', [PaymentController::class, 'refund'])->name('payments.refund');
    Route::get('payments', [PaymentController::class, 'index'])->name('payments.index');

    // Schedules
    Route::resource('schedules', LessonScheduleController::class);

    // Homeworks
    Route::post('homeworks/{homework}/publish', [HomeworkController::class, 'publish'])->name('homeworks.publish');
    Route::post('homeworks/{homework}/close', [HomeworkController::class, 'close'])->name('homeworks.close');
    Route::resource('homeworks', HomeworkController::class);
    Route::get('homeworks/{homework}/submissions', [HomeworkSubmissionController::class, 'index'])->name('homeworks.submissions.index');
    Route::get('homeworks/{homework}/submissions/{submission}', [HomeworkSubmissionController::class, 'show'])->name('homeworks.submissions.show');
    Route::post('homeworks/{homework}/submissions/{submission}/grade', [HomeworkSubmissionController::class, 'grade'])->name('homeworks.submissions.grade');

    // Education - Announcements
    Route::middleware(['permission:announcements.view'])->group(function () {
        Route::post('announcements/{announcement}/publish', [AnnouncementController::class, 'publish'])->name('announcements.publish');
        Route::resource('announcements', AnnouncementController::class)->only(['index', 'store', 'destroy']);
    });

    // Education - Attendance
    Route::middleware(['permission:attendance.view'])->group(function () {
        Route::get('attendance/report', [AttendanceController::class, 'report'])->name('attendance.report');
        Route::get('attendance/{attendance}/take', [AttendanceController::class, 'take'])->name('attendance.take');
        Route::post('attendance/{attendance}/take', [AttendanceController::class, 'storeBulk'])->name('attendance.storeBulk');
        Route::resource('attendance', AttendanceController::class)->only(['index', 'store', 'show']);
    });

    // Education - Homework & Assignments
    Route::middleware(['permission:homeworks.view'])->group(function () {
        Route::get('assignments/analytics', [AssignmentSubmissionController::class, 'analytics'])->name('assignments.analytics');
        Route::get('assignments/{assignment}/submissions', [AssignmentSubmissionController::class, 'index'])->name('assignments.submissions.index');
        Route::post('assignments/{assignment}/submissions', [AssignmentSubmissionController::class, 'store'])->name('assignments.submissions.store');
        Route::post('assignments/{assignment}/evaluate', [AssignmentSubmissionController::class, 'evaluate'])->name('assignments.submissions.evaluate');
        Route::resource('assignments', AssignmentController::class)->only(['index', 'store']);
    });

    // Education - Finance & Invoices
    Route::middleware(['permission:finance.view'])->group(function () {
        Route::get('invoices/dashboard', [InvoiceController::class, 'dashboard'])->name('invoices.dashboard');
        Route::post('invoices/{invoice}/cancel', [InvoiceController::class, 'cancel'])->name('invoices.cancel');
        Route::resource('invoices', InvoiceController::class)->only(['index', 'store', 'show']);
        Route::post('payments', [PaymentController::class, 'store'])->name('payments.store');
        
        // P0 Sprint 6.2 - Core Finance Lifecycle
        Route::resource('discounts', \App\Http\Controllers\Admin\DiscountController::class);
        Route::resource('scholarships', \App\Http\Controllers\Admin\ScholarshipController::class);
        Route::resource('refunds', \App\Http\Controllers\Admin\RefundController::class);
        Route::resource('payment-plans', \App\Http\Controllers\Admin\PaymentPlanController::class);
    });

    // Education - Teachers
    Route::middleware(['permission:teachers.view'])->group(function () {
        Route::get('teachers/{teacher}/performance', [TeacherPerformanceController::class, 'show'])->name('teachers.performance');
        Route::post('teachers/performance', [TeacherPerformanceController::class, 'store'])->name('teachers.performance.store');
        Route::get('teachers/{teacher}/analytics', [TeacherController::class, 'analytics'])->name('teachers.analytics');
        Route::resource('teachers', TeacherController::class)->except(['show']);
        Route::resource('teachers-schedules', TeacherScheduleController::class)->only(['index', 'store'])->names([
            'index' => 'teachers.schedules.index',
            'store' => 'teachers.schedules.store',
        ]);
        Route::resource('teachers-salary', TeacherSalaryController::class)->only(['index', 'store'])->names([
            'index' => 'teachers.salary.index',
            'store' => 'teachers.salary.store',
        ]);
        Route::resource('teachers-contracts', TeacherContractController::class)->only(['index', 'store'])->names([
            'index' => 'teachers.contracts.index',
            'store' => 'teachers.contracts.store',
        ]);
    });
    
    // Teacher show route without teachers.view middleware so they can view themselves
    Route::get('teachers/{teacher}', [TeacherController::class, 'show'])->name('teachers.show');

    // Education - Courses
    Route::middleware(['permission:courses.view'])->group(function () {
        Route::get('courses/analytics', [CourseController::class, 'analytics'])->name('courses.analytics');
        Route::resource('courses-levels', CourseLevelController::class)->only(['index', 'store', 'destroy'])->names([
            'index' => 'courses.levels.index',
            'store' => 'courses.levels.store',
            'destroy' => 'courses.levels.destroy',
        ]);
        Route::resource('courses', CourseController::class)->except(['show']);
    });

    // Education - Classrooms
    Route::middleware(['permission:classrooms.view'])->group(function () {
        Route::get('classrooms/analytics', [ClassroomController::class, 'analytics'])->name('classrooms.analytics');
        Route::get('classrooms/academic-calendar', [AcademicCalendarController::class, 'index'])->name('classrooms.academic-calendar.index');
        Route::post('classrooms/academic-calendar', [AcademicCalendarController::class, 'store'])->name('classrooms.academic-calendar.store');
        Route::get('classrooms/schedules', [ClassScheduleController::class, 'index'])->name('classrooms.schedules.index');
        Route::post('classrooms/schedules', [ClassScheduleController::class, 'store'])->name('classrooms.schedules.store');
        Route::get('classrooms/holidays', [HolidayController::class, 'index'])->name('classrooms.holidays.index');
        Route::post('classrooms/holidays', [HolidayController::class, 'store'])->name('classrooms.holidays.store');
        
        // Students in Classroom
        Route::get('classrooms/{classroom}/students', [ClassroomController::class, 'students'])->name('classrooms.students');
        Route::post('classrooms/{classroom}/students/attach', [ClassroomController::class, 'attachStudents'])->name('classrooms.students.attach');
        Route::post('classrooms/{classroom}/students/detach', [ClassroomController::class, 'detachStudents'])->name('classrooms.students.detach');
        
        Route::resource('classrooms', ClassroomController::class);
    });

    // System Settings
    Route::middleware(['permission:settings.view'])->group(function () {
        Route::get('settings', [PlatformSettingController::class, 'index'])->name('settings.index');
        Route::post('settings', [PlatformSettingController::class, 'update'])->name('settings.update');
        Route::post('settings/test-mail', [PlatformSettingController::class, 'testMail'])->name('settings.test-mail');
        Route::post('settings/test-storage', [PlatformSettingController::class, 'testStorage'])->name('settings.test-storage');
        Route::get('settings/export', [PlatformSettingController::class, 'export'])->name('settings.export');
        Route::post('settings/import', [PlatformSettingController::class, 'import'])->name('settings.import');
        Route::post('settings/reset', [PlatformSettingController::class, 'reset'])->name('settings.reset');
        
        // License & Subscription Management
        Route::get('licenses', [LicenseController::class, 'index'])->name('licenses.index');
        Route::post('licenses/activate', [LicenseController::class, 'activate'])->name('licenses.activate');
        Route::post('licenses/change-plan', [LicenseController::class, 'changePlan'])->name('licenses.change-plan');
        Route::post('licenses/pay', [LicenseController::class, 'pay'])->name('licenses.pay');
    });

    // SaaS Operations (Super Admin Only)
    Route::middleware(['role:Super Admin'])->prefix('saas')->name('saas.')->group(function () {
        Route::get('tenants', [SaaSTenantController::class, 'index'])->name('tenants.index');
        Route::get('tenants/{tenant}', [SaaSTenantController::class, 'show'])->name('tenants.show');
        Route::post('tenants/{tenant}/suspend', [SaaSTenantController::class, 'suspend'])->name('tenants.suspend');
        Route::post('tenants/{tenant}/activate', [SaaSTenantController::class, 'activate'])->name('tenants.activate');
        Route::get('system-health', [SaaSHealthController::class, 'index'])->name('system-health.index');
    });

    Route::middleware(['role:Super Admin'])->prefix('platform')->name('platform.')->group(function () {
        Route::get('subscriptions', [SubscriptionController::class, 'index'])->name('subscriptions.index');
        Route::get('subscriptions/plans', [SubscriptionController::class, 'plans'])->name('subscriptions.plans');
        Route::get('subscriptions/plans/create', [SubscriptionController::class, 'createPlan'])->name('subscriptions.plans.create');
        Route::post('subscriptions/plans', [SubscriptionController::class, 'storePlan'])->name('subscriptions.plans.store');
        Route::get('subscriptions/plans/{plan}', [SubscriptionController::class, 'showPlan'])->name('subscriptions.plans.show');
        Route::get('subscriptions/plans/{plan}/edit', [SubscriptionController::class, 'editPlan'])->name('subscriptions.plans.edit');
        Route::put('subscriptions/plans/{plan}', [SubscriptionController::class, 'updatePlan'])->name('subscriptions.plans.update');
        Route::post('subscriptions/assign', [SubscriptionController::class, 'assign'])->name('subscriptions.assign');
        Route::post('subscriptions/change-plan', [SubscriptionController::class, 'changePlan'])->name('subscriptions.change-plan');
        Route::post('subscriptions/cancel', [SubscriptionController::class, 'cancel'])->name('subscriptions.cancel');
    });

    // Reporting & BI
    Route::middleware(['permission:dashboard.view'])->group(function () {
        Route::get('reporting/dashboard', [ExecutiveDashboardController::class, 'index'])->name('reporting.dashboard');
        Route::get('reporting/analytics', [ExecutiveDashboardController::class, 'analytics'])->name('reporting.analytics');
        Route::post('reporting/snapshot', [ExecutiveDashboardController::class, 'snapshot'])->name('reporting.snapshot');
        Route::get('reporting/reports', [ReportController::class, 'index'])->name('reporting.reports');
        Route::post('reporting/reports/schedules', [ReportController::class, 'storeSchedule'])->name('reporting.schedules.store');
        Route::post('reporting/reports/export', [ReportController::class, 'export'])->name('reporting.export');
        Route::get('reporting/reports/{id}/download', [ReportController::class, 'download'])->name('reporting.download');
    });

    // CRM
    Route::middleware(['permission:crm.view'])->group(function () {
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
        Route::resource('leads', LeadController::class)->only(['index', 'store', 'show', 'update']);
    });

    // Admission & Enrollment
    Route::middleware(['permission:admission.view'])->group(function () {
        Route::get('admission/dashboard', [AdmissionController::class, 'dashboard'])->name('admission.dashboard');
        Route::get('admission/workflow', [AdmissionController::class, 'workflow'])->name('admission.workflow');
        Route::get('admission/documents', [AdmissionController::class, 'documents'])->name('admission.documents');
        Route::post('admission/convert/{leadId}', [AdmissionController::class, 'convertLead'])->name('admission.convert');
        Route::post('admission/{id}/status', [AdmissionController::class, 'updateStatus'])->name('admission.status.update');
        Route::post('admission/{id}/note', [AdmissionController::class, 'storeNote'])->name('admission.note.store');
        Route::resource('admission', AdmissionController::class)->only(['index', 'store', 'show']);

        Route::post('enrollment/complete', [EnrollmentController::class, 'complete'])->name('enrollment.complete');
        Route::post('enrollment/document/upload', [EnrollmentController::class, 'uploadDocument'])->name('enrollment.document.upload');
        Route::post('enrollment/document/{id}/approve', [EnrollmentController::class, 'approveDocument'])->name('enrollment.document.approve');

        Route::get('contracts', [ContractController::class, 'index'])->name('contracts.index');
        Route::post('contracts/generate', [ContractController::class, 'generate'])->name('contracts.generate');
        Route::post('contracts/{id}/sign', [ContractController::class, 'sign'])->name('contracts.sign');
    });

    // HR Module
    Route::middleware(['permission:hr.view'])->group(function () {
        Route::get('hr/dashboard', [HRAnalyticsController::class, 'dashboard'])->name('hr.dashboard');
        Route::get('hr/analytics', [HRAnalyticsController::class, 'index'])->name('hr.analytics');

        Route::post('departments/position', [DepartmentController::class, 'storePosition'])->name('positions.store');
        Route::resource('departments', DepartmentController::class)->only(['index', 'store']);
        Route::resource('employees', EmployeeController::class)->only(['index', 'store', 'update', 'destroy']);

        Route::post('payroll/{id}/approve', [PayrollController::class, 'approve'])->name('payroll.approve');
        Route::post('payroll/{id}/pay', [PayrollController::class, 'pay'])->name('payroll.pay');
        Route::resource('payroll', PayrollController::class)->only(['index', 'store']);

        Route::post('leaves/{id}/approve', [LeaveController::class, 'approve'])->name('leaves.approve');
        Route::post('leaves/{id}/reject', [LeaveController::class, 'reject'])->name('leaves.reject');
        Route::resource('leaves', LeaveController::class)->only(['index', 'store']);

        Route::resource('hr/attendance', EmployeeAttendanceController::class)->names('hr.attendance')->only(['index', 'store']);

        Route::post('expenses/{id}/approve', [ExpenseController::class, 'approve'])->name('expenses.approve');
        Route::post('expenses/{id}/reject', [ExpenseController::class, 'reject'])->name('expenses.reject');
        Route::resource('expenses', ExpenseController::class)->only(['index', 'store']);

        Route::post('advances/{id}/approve', [AdvanceController::class, 'approve'])->name('advances.approve');
        Route::post('advances/{id}/reject', [AdvanceController::class, 'reject'])->name('advances.reject');
        Route::resource('advances', AdvanceController::class)->only(['index', 'store']);

        Route::resource('performance', PerformanceController::class)->only(['index', 'store']);
    });

    // Inventory & Asset Module
    Route::middleware(['permission:assets.view'])->group(function () {
        Route::get('inventory/dashboard', [InvAnalyticsController::class, 'dashboard'])->name('inventory.dashboard');
        Route::get('inventory/analytics', [InvAnalyticsController::class, 'index'])->name('inventory.analytics');

        Route::post('assets/assign', [AssetController::class, 'assign'])->name('assets.assign');
        Route::post('assets/return/{id}', [AssetController::class, 'returnAssignment'])->name('assets.return');
        Route::post('assets/{id}/retire', [AssetController::class, 'retire'])->name('assets.retire');
        Route::resource('assets', AssetController::class)->only(['index', 'store', 'update', 'destroy']);

        Route::post('inventory/categories/location', [AssetCategoryController::class, 'storeLocation'])->name('inventory.categories.store-location');
        Route::resource('inventory/categories', AssetCategoryController::class)->only(['index', 'store'])->names('inventory.categories');

        Route::post('inventory/transaction', [InventoryController::class, 'transaction'])->name('inventory.transaction');
        Route::post('inventory/category', [InventoryController::class, 'storeCategory'])->name('inventory.store-category');
        Route::post('inventory/warehouse', [InventoryController::class, 'storeWarehouse'])->name('inventory.store-warehouse');
        Route::resource('inventory', InventoryController::class)->only(['index', 'store']);

        Route::resource('suppliers', SupplierController::class)->only(['index', 'store', 'update', 'destroy']);

        Route::post('purchase/{id}/approve', [PurchaseController::class, 'approve'])->name('purchase.approve');
        Route::post('purchase/{id}/complete', [PurchaseController::class, 'complete'])->name('purchase.complete');
        Route::resource('purchase', PurchaseController::class)->only(['index', 'store']);

        Route::post('maintenance/{id}/complete', [MaintenanceController::class, 'complete'])->name('maintenance.complete');
        Route::resource('maintenance', MaintenanceController::class)->only(['index', 'store']);

        Route::resource('transfers', TransferController::class)->only(['index', 'store']);
    });

    // Document Management & Digital Archive
    Route::middleware(['permission:documents.view'])->group(function () {
        Route::get('documents/dashboard', [DocumentController::class, 'dashboard'])->name('documents.dashboard');
        Route::get('documents/analytics', [DocumentSearchController::class, 'analytics'])->name('documents.analytics');
        Route::get('documents/search', [DocumentSearchController::class, 'search'])->name('documents.search');
        Route::post('documents/{id}/version', [DocumentController::class, 'uploadVersion'])->name('documents.version');
        Route::post('documents/{id}/share', [DocumentController::class, 'share'])->name('documents.share');
        Route::get('documents/{id}/download', [DocumentController::class, 'download'])->name('documents.download');
        Route::post('documents/{id}/restore', [DocumentController::class, 'restore'])->name('documents.restore');
        Route::resource('documents', DocumentController::class)->except(['edit']);

        Route::resource('document-categories', DocumentCategoryController::class)->only(['index', 'store', 'update', 'destroy']);
        
        // Guidance
        Route::resource('guidance', \App\Http\Controllers\Admin\GuidanceController::class);
        Route::resource('meetings', \App\Http\Controllers\Admin\MeetingController::class);
        Route::resource('goals', \App\Http\Controllers\Admin\GoalController::class);
    });

    // Attendance Management
    Route::middleware(['permission:attendance.view'])->group(function () {
        Route::get('attendance/report', [\App\Http\Controllers\Admin\AttendanceController::class, 'report'])->name('attendance.report');
        Route::resource('attendance', \App\Http\Controllers\Admin\AttendanceController::class)->except(['edit', 'update', 'destroy']);
    });

    // Schedule Management
    Route::middleware(['permission:schedule.view'])->group(function () {
        Route::resource('schedule', \App\Http\Controllers\Admin\ScheduleController::class);
    });
});
