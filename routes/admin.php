<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\UserProfileController;
use App\Http\Controllers\Admin\UserPreferenceController;
use App\Http\Controllers\Admin\ExecutiveDashboardController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\AdmissionController;
use App\Http\Controllers\Admin\EnrollmentController;
use App\Http\Controllers\Admin\ContractController;
use App\Http\Controllers\Admin\EmployeeController;
use App\Http\Controllers\Admin\LeaveController;
use App\Http\Controllers\Admin\EmployeeAttendanceController;
use App\Http\Controllers\Admin\ExpenseController;
use App\Http\Controllers\Admin\PerformanceController;
use App\Http\Controllers\Admin\AnalyticsController as HRAnalyticsController;
use App\Http\Controllers\Admin\LessonScheduleController;
use App\Http\Controllers\Admin\HomeworkController;
use App\Http\Controllers\Admin\HomeworkSubmissionController;
use App\Http\Controllers\Admin\FinanceController;
use App\Http\Controllers\Admin\PreRegistrationController;
use App\Http\Controllers\Admin\FinanceDashboardController;
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
use App\Http\Controllers\Admin\LicenseController;
use App\Http\Controllers\Admin\TeacherController;
use App\Http\Controllers\Admin\TeacherScheduleController;
use App\Http\Controllers\Admin\TeacherPerformanceController;
use App\Http\Controllers\Admin\CourseController;
use App\Http\Controllers\Admin\ClassroomController;
use App\Http\Controllers\Admin\AcademicCalendarController;
use App\Http\Controllers\Admin\ClassScheduleController;
use App\Http\Controllers\Admin\HolidayController;
use App\Http\Controllers\Admin\StudentController;
use App\Http\Controllers\Admin\AttendanceController;
use App\Http\Controllers\Admin\ExamController;
use App\Http\Controllers\Admin\ExamResultController;
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
Route::middleware(['auth', 'role:Super Admin|Admin|Branch Admin|Tenant Admin|tenant_admin|staff|admin|Teacher'])->prefix('admin')->name('admin.')->group(function () {
    
    // Onboarding Wizard Routes (Sprint 10.2 / 10.8)
    Route::prefix('onboarding')->name('onboarding.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Admin\OnboardingController::class, 'index'])->name('index');
        Route::get('/profile', [\App\Http\Controllers\Admin\OnboardingController::class, 'profile'])->name('profile');
        Route::post('/profile', [\App\Http\Controllers\Admin\OnboardingController::class, 'saveProfile'])->name('saveProfile');
        Route::get('/academic-year', [\App\Http\Controllers\Admin\OnboardingController::class, 'academicYear'])->name('academic-year');
        Route::post('/academic-year', [\App\Http\Controllers\Admin\OnboardingController::class, 'saveAcademicYear'])->name('saveAcademicYear');
        Route::get('/teacher', [\App\Http\Controllers\Admin\OnboardingController::class, 'teacher'])->name('teacher');
        Route::post('/teacher', [\App\Http\Controllers\Admin\OnboardingController::class, 'createTeacher'])->name('createTeacher');
        Route::get('/classroom', [\App\Http\Controllers\Admin\OnboardingController::class, 'classroom'])->name('classroom');
        Route::post('/classroom', [\App\Http\Controllers\Admin\OnboardingController::class, 'createClassroom'])->name('createClassroom');
        Route::get('/complete', [\App\Http\Controllers\Admin\OnboardingController::class, 'complete'])->name('complete');

        // Legacy compatibility routes
        Route::post('/identity', [\App\Http\Controllers\Admin\OnboardingController::class, 'saveProfile'])->name('identity');
        Route::post('/term', [\App\Http\Controllers\Admin\OnboardingController::class, 'saveAcademicYear'])->name('term');
    });

    // Branch Switch
    Route::post('/branch/switch', function (\Illuminate\Http\Request $request) {
        $request->validate(['branch_id' => 'required|exists:branches,id']);
        if (!auth()->user()->hasRole('Super Admin') && auth()->user()->branch_id != $request->branch_id) {
            abort(403);
        }
        $branch = \App\Models\Branch::find($request->branch_id);
        session(['active_branch_id' => $branch->id, 'active_branch_name' => $branch->name]);
        return back();
    })->name('branch.switch');

    // Profiles & Preferences
    Route::get('profile', [UserProfileController::class, 'edit'])->name('profile.edit');
    Route::post('profile', [UserProfileController::class, 'update'])->name('profile.update');
    Route::post('profile/password', [UserProfileController::class, 'password'])->name('profile.password');
    Route::post('profile/avatar', [UserProfileController::class, 'avatar'])->name('profile.avatar');
    Route::post('preferences', [UserPreferenceController::class, 'update'])->name('preferences.update');

    // Access Management - Users & Activity Logs
    Route::middleware(['permission:users.view'])->group(function () {
        Route::get('activity-logs', [\App\Http\Controllers\Admin\ActivityLogController::class, 'index'])->name('activity-logs.index');
        Route::get('subscription', [\App\Http\Controllers\Admin\SubscriptionDashboardController::class, 'index'])->name('subscription.index');
        Route::get('licenses', [\App\Http\Controllers\Admin\LicenseController::class, 'index'])->name('licenses.index');
        Route::post('licenses/{license}/activate', [\App\Http\Controllers\Admin\LicenseController::class, 'activate'])->name('licenses.activate');
        Route::post('licenses/{license}/renew', [\App\Http\Controllers\Admin\LicenseController::class, 'renew'])->name('licenses.renew');
        Route::post('licenses/{license}/suspend', [\App\Http\Controllers\Admin\LicenseController::class, 'suspend'])->name('licenses.suspend');
        Route::post('licenses/{license}/cancel', [\App\Http\Controllers\Admin\LicenseController::class, 'cancel'])->name('licenses.cancel');
        Route::post('users/bulk', [UserController::class, 'bulk'])->name('users.bulk');
        Route::post('users/{id}/restore', [UserController::class, 'restore'])->name('users.restore');
        Route::post('users/{user}/status', [UserController::class, 'toggleStatus'])->name('users.status');
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

    // Notifications & Center
    Route::middleware(['permission:notifications.view'])->group(function () {
        Route::get('notifications/dashboard', [NotificationController::class, 'dashboard'])->name('notifications.dashboard');
        Route::get('notifications/preferences', [NotificationController::class, 'preferences'])->name('notifications.preferences');
        Route::put('notifications/preferences', [NotificationController::class, 'updatePreferences'])->name('notifications.preferences.update');
        Route::get('notifications/templates', [NotificationTemplateController::class, 'index'])->name('notifications.templates');
        Route::post('notifications/templates', [NotificationTemplateController::class, 'store'])->name('notifications.templates.store');
        Route::get('notifications/analytics', NotificationAnalyticsController::class)->name('notifications.analytics');
        Route::post('notifications/{notification}/read', [NotificationController::class, 'markRead'])->name('notifications.read');
        Route::post('notifications/read-all', [NotificationController::class, 'markAllRead'])->name('notifications.read-all');
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

    // Education - Students & Parents
    Route::middleware(['permission:students.view'])->group(function () {
        Route::get('students/analytics', [StudentController::class, 'analytics'])->name('students.analytics');
        Route::resource('students', StudentController::class);
        Route::get('parents/{id}', [\App\Http\Controllers\Admin\ParentController::class, 'show'])->name('parents.show');
    });
        
    // Exams & Academic Analytics
    Route::middleware(['feature.access:exam'])->group(function () {
        Route::get('exams/{exam}/analysis', [ExamController::class, 'analysis'])->name('exams.analysis');
        Route::get('exams/{exam}/results', [\App\Http\Controllers\Admin\ExamResultController::class, 'index'])->name('exams.results');
        Route::post('exams/{exam}/results', [\App\Http\Controllers\Admin\ExamResultController::class, 'store'])->name('exams.results.store');
        Route::post('exam-results/{result}/branches', [ExamController::class, 'storeBranchResults'])->name('exams.results.branches');
        Route::get('students/{student}/academic-analytics', [ExamController::class, 'studentAnalytics'])->name('students.academic-analytics');
        Route::resource('exams', ExamController::class);
    });

    // Finance & Invoices
    Route::middleware(['feature.access:finance'])->group(function () {
        Route::get('finance/dashboard', [FinanceDashboardController::class, 'index'])->name('finance.dashboard');
        Route::get('invoices/dashboard', [FinanceDashboardController::class, 'index'])->name('invoices.dashboard');
        Route::get('invoices/search-students', [InvoiceController::class, 'searchStudents'])->name('invoices.search-students');
        Route::post('invoices/{invoice}/payments', [InvoiceController::class, 'storePayment'])->name('invoices.payments.store');
        Route::delete('payments/{payment}', [InvoiceController::class, 'destroyPayment'])->name('invoices.payments.destroy');
        Route::resource('invoices', InvoiceController::class);
        
        Route::get('pre-registrations/{pre_registration}/convert', [PreRegistrationController::class, 'showConvertForm'])->name('pre-registrations.convert');
        Route::post('pre-registrations/{pre_registration}/convert', [PreRegistrationController::class, 'convertToStudent'])->name('pre-registrations.convert.store');
        Route::resource('pre-registrations', PreRegistrationController::class);

        Route::post('finance/{plan}/discount', [FinanceController::class, 'applyDiscount'])->name('finance.discount');
        Route::resource('finance', FinanceController::class);
        Route::post('installments/{installment}/collect', [InstallmentController::class, 'collect'])->name('installments.collect');
        Route::get('installments', [InstallmentController::class, 'index'])->name('installments.index');
        Route::post('payments/{payment}/refund', [PaymentController::class, 'refund'])->name('payments.refund');
        Route::get('payments', [PaymentController::class, 'index'])->name('payments.index');
    });

    // Schedules
    Route::middleware(['feature.access:schedule'])->group(function () {
        Route::resource('schedules', LessonScheduleController::class);
    });

    // Homeworks (Primary Homework System)
    Route::middleware(['feature.access:homework'])->group(function () {
        Route::post('homeworks/{homework}/publish', [HomeworkController::class, 'publish'])->name('homeworks.publish');
        Route::post('homeworks/{homework}/close', [HomeworkController::class, 'close'])->name('homeworks.close');
        Route::post('homework-submissions/{submission}/progress', [HomeworkController::class, 'updateTaskProgress'])->name('homeworks.update-task-progress');
        Route::resource('homeworks', HomeworkController::class);
        Route::get('homeworks/{homework}/submissions', [HomeworkSubmissionController::class, 'index'])->name('homeworks.submissions.index');
        Route::get('homeworks/{homework}/submissions/{submission}', [HomeworkSubmissionController::class, 'show'])->name('homeworks.submissions.show');
        Route::post('homeworks/{homework}/submissions/{submission}/grade', [HomeworkSubmissionController::class, 'grade'])->name('homeworks.submissions.grade');
    });

    // Education - Announcements (CMS Official Module)
    Route::middleware(['permission:announcements.view'])->group(function () {
        Route::post('announcements/{announcement}/publish', [AnnouncementController::class, 'publish'])->name('announcements.publish');
        Route::post('announcements/{announcement}/archive', [AnnouncementController::class, 'archive'])->name('announcements.archive');
        Route::post('announcements/{announcement}/popup-seen', [AnnouncementController::class, 'markPopupSeen'])->name('announcements.popup-seen');
        Route::resource('announcements', AnnouncementController::class);
    });

    // Education - Attendance
    Route::middleware(['permission:attendance.view', 'feature.access:attendance'])->group(function () {
        Route::get('attendance/report', [AttendanceController::class, 'report'])->name('attendance.report');
        Route::get('attendance/{attendance}/take', [AttendanceController::class, 'take'])->name('attendance.take');
        Route::post('attendance/{attendance}/take', [AttendanceController::class, 'storeBulk'])->name('attendance.storeBulk');
        Route::resource('attendance', AttendanceController::class)->only(['index', 'store', 'show']);
    });

    // Education - Finance & Invoices
    Route::middleware(['permission:finance.view', 'feature.access:finance'])->group(function () {
        Route::get('invoices/dashboard', [InvoiceController::class, 'dashboard'])->name('invoices.dashboard');
        Route::post('invoices/{invoice}/cancel', [InvoiceController::class, 'cancel'])->name('invoices.cancel');
        Route::resource('invoices', InvoiceController::class)->only(['index', 'store', 'show']);
        Route::post('payments', [PaymentController::class, 'store'])->name('payments.store');
        
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
    });
    
    Route::get('teachers/{teacher}', [TeacherController::class, 'show'])->name('teachers.show');

    // Education - Courses
    Route::middleware(['permission:courses.view'])->group(function () {
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
        
        Route::get('classrooms/{classroom}/students', [ClassroomController::class, 'students'])->name('classrooms.students');
        Route::post('classrooms/{classroom}/students/attach', [ClassroomController::class, 'attachStudents'])->name('classrooms.students.attach');
        Route::post('classrooms/{classroom}/students/detach', [ClassroomController::class, 'detachStudents'])->name('classrooms.students.detach');
        
        Route::resource('classrooms', ClassroomController::class);
    });

    // HR Module
    Route::middleware(['permission:hr.view'])->group(function () {
        Route::get('hr/dashboard', [HRAnalyticsController::class, 'dashboard'])->name('hr.dashboard');
        Route::get('hr/analytics', [HRAnalyticsController::class, 'index'])->name('hr.analytics');

        Route::resource('employees', EmployeeController::class)->only(['index', 'store', 'update', 'destroy']);
        Route::resource('leaves', LeaveController::class)->only(['index', 'store']);
        Route::resource('hr/attendance', EmployeeAttendanceController::class)->names('hr.attendance')->only(['index', 'store']);
        Route::resource('expenses', ExpenseController::class)->only(['index', 'store']);
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
        Route::middleware(['feature.access:guidance'])->group(function () {
            Route::resource('guidance', \App\Http\Controllers\Admin\GuidanceController::class);
            Route::resource('meetings', \App\Http\Controllers\Admin\MeetingController::class);
            Route::resource('goals', \App\Http\Controllers\Admin\GoalController::class);
        });
    });

    // Attendance Management
    Route::middleware(['permission:attendance.view', 'feature.access:attendance'])->group(function () {
        Route::get('attendance/report', [\App\Http\Controllers\Admin\AttendanceController::class, 'report'])->name('attendance.report');
        Route::resource('attendance', \App\Http\Controllers\Admin\AttendanceController::class)->except(['edit', 'update', 'destroy']);
    });

    // Schedule Management
    Route::middleware(['permission:schedule.view', 'feature.access:schedule'])->group(function () {
        Route::resource('schedule', \App\Http\Controllers\Admin\ScheduleController::class);
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
    });

    // Reporting & BI
    Route::middleware(['permission:dashboard.view'])->group(function () {
        Route::get('reporting/dashboard', [ExecutiveDashboardController::class, 'index'])->name('reporting.dashboard');
        Route::get('reporting/analytics', [ExecutiveDashboardController::class, 'analytics'])->name('reporting.analytics');
        Route::post('reporting/snapshot', [ExecutiveDashboardController::class, 'snapshot'])->name('reporting.snapshot');
        Route::get('reporting/reports', [ReportController::class, 'index'])->name('reporting.reports');
        Route::post('reporting/reports/export', [ReportController::class, 'export'])->name('reporting.export');
        Route::get('reporting/reports/{id}/download', [ReportController::class, 'download'])->name('reporting.download');
    });

    // Institution Configuration Management
    Route::middleware(['role:Super Admin|Admin|Branch Admin|Tenant Admin|tenant_admin|admin'])->prefix('settings/institution')->name('settings.institution.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Admin\InstitutionSettingController::class, 'index'])->name('index');
        Route::post('/general', [\App\Http\Controllers\Admin\InstitutionSettingController::class, 'updateGeneral'])->name('updateGeneral');
        Route::post('/branding', [\App\Http\Controllers\Admin\InstitutionSettingController::class, 'updateBranding'])->name('updateBranding');
        Route::post('/regional', [\App\Http\Controllers\Admin\InstitutionSettingController::class, 'updateRegional'])->name('updateRegional');
        Route::post('/notifications', [\App\Http\Controllers\Admin\InstitutionSettingController::class, 'updateNotifications'])->name('updateNotifications');
    });
});
