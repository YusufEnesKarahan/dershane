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

// Admin Framework Routes
Route::middleware(['auth'])->prefix('admin')->name('admin.')->group(function () {
    
    // SaaS Platform Control Layer
    Route::middleware(['role:Super Admin'])->group(function () {
        Route::get('platform/licenses', [\App\Http\Controllers\Admin\LicenseController::class, 'index'])->name('platform.licenses.index');
        Route::get('platform/features', [\App\Http\Controllers\Admin\FeatureFlagController::class, 'index'])->name('platform.features.index');
        Route::post('platform/features/{feature}/toggle', [\App\Http\Controllers\Admin\FeatureFlagController::class, 'toggle'])->name('platform.features.toggle');
        Route::get('platform/license-status', [\App\Http\Controllers\Admin\LicenseStatusController::class, 'index'])->name('platform.license_status.index');
        Route::get('platform/updates', [\App\Http\Controllers\Admin\HQUpdateController::class, 'index'])->name('platform.updates.index');
        Route::post('platform/updates/check', [\App\Http\Controllers\Admin\HQUpdateController::class, 'check'])->name('platform.updates.check');
        Route::post('platform/updates/{update}/install', [\App\Http\Controllers\Admin\HQUpdateController::class, 'markInstalled'])->name('platform.updates.mark-installed');
        
        Route::get('platform/hq-central', [\App\Http\Controllers\Admin\HQCentralController::class, 'index'])->name('platform.hq_central.index');
        Route::get('platform/hq-central/systems', [\App\Http\Controllers\Admin\HQSystemController::class, 'index'])->name('platform.hq_central.systems.index');
        Route::get('platform/hq-central/systems/{system}', [\App\Http\Controllers\Admin\HQSystemController::class, 'show'])->name('platform.hq_central.systems.show');
        
        Route::get('platform/hq-central/commands', [\App\Http\Controllers\Admin\HQRemoteCommandController::class, 'index'])->name('hq.commands.index');
        Route::get('platform/hq-central/commands/create', [\App\Http\Controllers\Admin\HQRemoteCommandController::class, 'create'])->name('hq.commands.create');
        Route::post('commands/{command}/retry', [\App\Http\Controllers\Admin\HQRemoteCommandController::class, 'retry'])->name('commands.retry');
        
        // Version Management
        Route::get('platform/hq-central/versions', [\App\Http\Controllers\Admin\HQVersionController::class, 'index'])->name('platform.hq_central.versions.index');
        Route::get('platform/hq-central/versions/create', [\App\Http\Controllers\Admin\HQVersionController::class, 'create'])->name('platform.hq_central.versions.create');
        Route::post('platform/hq-central/versions', [\App\Http\Controllers\Admin\HQVersionController::class, 'store'])->name('platform.hq_central.versions.store');
        Route::get('platform/hq-central/versions/{version}', [\App\Http\Controllers\Admin\HQVersionController::class, 'show'])->name('platform.hq_central.versions.show');
        Route::post('platform/hq-central/versions/{version}/archive', [\App\Http\Controllers\Admin\HQVersionController::class, 'archive'])->name('platform.hq_central.versions.archive');
        
        // Update Management
        Route::get('platform/hq-central/updates', [\App\Http\Controllers\Admin\HQUpdateController::class, 'index'])->name('platform.hq_central.updates.index');
        Route::get('platform/hq-central/updates/{update}', [\App\Http\Controllers\Admin\HQUpdateController::class, 'show'])->name('platform.hq_central.updates.show');
        Route::post('platform/hq-central/updates', [\App\Http\Controllers\Admin\HQUpdateController::class, 'store'])->name('platform.hq_central.updates.store');
        Route::post('platform/hq-central/updates/{update}/cancel', [\App\Http\Controllers\Admin\HQUpdateController::class, 'cancel'])->name('platform.hq_central.updates.cancel');
        Route::post('platform/hq-central/updates/{update}/retry', [\App\Http\Controllers\Admin\HQUpdateController::class, 'retry'])->name('platform.hq_central.updates.retry');
        
        // Configuration Management
        Route::get('platform/hq-central/configurations', [\App\Http\Controllers\Admin\HQConfigurationController::class, 'index'])->name('platform.hq_central.configurations.index');
        Route::get('platform/hq-central/configurations/create', [\App\Http\Controllers\Admin\HQConfigurationController::class, 'create'])->name('platform.hq_central.configurations.create');
        Route::post('platform/hq-central/configurations', [\App\Http\Controllers\Admin\HQConfigurationController::class, 'store'])->name('platform.hq_central.configurations.store');
        Route::get('platform/hq-central/configurations/{configuration}', [\App\Http\Controllers\Admin\HQConfigurationController::class, 'show'])->name('platform.hq_central.configurations.show');
        Route::post('platform/hq-central/configurations/{configuration}/items', [\App\Http\Controllers\Admin\HQConfigurationController::class, 'storeItem'])->name('platform.hq_central.configurations.items.store');
        Route::delete('platform/hq-central/configurations/{configuration}/items/{item}', [\App\Http\Controllers\Admin\HQConfigurationController::class, 'destroyItem'])->name('platform.hq_central.configurations.items.destroy');
        Route::get('platform/hq-central/configurations/{configuration}/history', [\App\Http\Controllers\Admin\HQConfigurationController::class, 'history'])->name('platform.hq_central.configurations.history');
        Route::post('platform/hq-central/configurations/{configuration}/version', [\App\Http\Controllers\Admin\HQConfigurationController::class, 'version'])->name('platform.hq_central.configurations.version');
        Route::get('platform/hq-central/configurations/{configuration}/rollback/{version}', [\App\Http\Controllers\Admin\HQConfigurationController::class, 'rollbackForm'])->name('platform.hq_central.configurations.rollback.form');
        Route::post('platform/hq-central/configurations/{configuration}/rollback/{version}', [\App\Http\Controllers\Admin\HQConfigurationController::class, 'rollback'])->name('platform.hq_central.configurations.rollback');

        // Backup Management        // HQ Backups & DR
        Route::get('platform/hq-central/backups', [\App\Http\Controllers\Admin\HQBackupController::class, 'index'])->name('platform.hq_central.backups.index');
        Route::get('platform/hq-central/backups/policies', [\App\Http\Controllers\Admin\HQBackupController::class, 'policies'])->name('platform.hq_central.backups.policies');
        Route::get('platform/hq-central/backups/jobs', [\App\Http\Controllers\Admin\HQBackupController::class, 'jobs'])->name('platform.hq_central.backups.jobs');
        Route::get('platform/hq-central/backups/snapshots', [\App\Http\Controllers\Admin\HQBackupController::class, 'snapshots'])->name('platform.hq_central.backups.snapshots');
        Route::get('platform/hq-central/backups/restores', [\App\Http\Controllers\Admin\HQBackupController::class, 'restores'])->name('platform.hq_central.backups.restores');
        Route::get('platform/hq-central/backups/storage', [\App\Http\Controllers\Admin\HQBackupController::class, 'storage'])->name('platform.hq_central.backups.storage');
        Route::get('platform/hq-central/backups/dr-plans', [\App\Http\Controllers\Admin\HQBackupController::class, 'drPlans'])->name('platform.hq_central.backups.dr_plans');

        // HQ Workflows
        // Tenant Management
        Route::get('platform/hq-central/tenants', [\App\Http\Controllers\Admin\HQTenantController::class, 'index'])->name('platform.hq_central.tenants.index');
        Route::post('platform/hq-central/tenants', [\App\Http\Controllers\Admin\HQTenantController::class, 'store'])->name('platform.hq_central.tenants.store');
        Route::put('platform/hq-central/tenants/{tenant}', [\App\Http\Controllers\Admin\HQTenantController::class, 'update'])->name('platform.hq_central.tenants.update');
        Route::delete('platform/hq-central/tenants/{tenant}', [\App\Http\Controllers\Admin\HQTenantController::class, 'destroy'])->name('platform.hq_central.tenants.destroy');
        Route::post('platform/hq-central/tenants/{tenant}/suspend', [\App\Http\Controllers\Admin\HQTenantController::class, 'suspend'])->name('platform.hq_central.tenants.suspend');
        Route::post('platform/hq-central/tenants/{tenant}/reactivate', [\App\Http\Controllers\Admin\HQTenantController::class, 'reactivate'])->name('platform.hq_central.tenants.reactivate');

        // Tenant Usage
        Route::get('platform/hq-central/tenants/{tenant}/usage', [\App\Http\Controllers\Admin\HQUsageController::class, 'show'])->name('platform.hq_central.tenants.usage');

        // HQ Audit Logs
        Route::get('platform/hq-central/audit', [\App\Http\Controllers\Admin\HQAuditController::class, 'index'])->name('platform.hq_central.audit.index');
        Route::get('platform/hq-central/audit/{auditLog}', [\App\Http\Controllers\Admin\HQAuditController::class, 'show'])->name('platform.hq_central.audit.show');
        
        // HQ Workflows
        Route::get('platform/hq-central/workflows', [\App\Http\Controllers\Admin\HQWorkflowController::class, 'index'])->name('platform.hq_central.workflows.index');
        Route::get('platform/hq-central/workflows/create', [\App\Http\Controllers\Admin\HQWorkflowController::class, 'create'])->name('platform.hq_central.workflows.create');
        Route::post('platform/hq-central/workflows', [\App\Http\Controllers\Admin\HQWorkflowController::class, 'store'])->name('platform.hq_central.workflows.store');
        Route::get('platform/hq-central/workflows/history', [\App\Http\Controllers\Admin\HQWorkflowController::class, 'history'])->name('platform.hq_central.workflows.history');
        Route::get('platform/hq-central/workflows/{workflow}', [\App\Http\Controllers\Admin\HQWorkflowController::class, 'show'])->name('platform.hq_central.workflows.show');

        // HQ Fleet & Deployments
        Route::get('platform/hq-central/fleet', [\App\Http\Controllers\Admin\HQFleetController::class, 'overview'])->name('platform.hq_central.fleet.overview');
        Route::get('platform/hq-central/fleet/deployments', [\App\Http\Controllers\Admin\HQFleetController::class, 'deployments'])->name('platform.hq_central.fleet.deployments');
        Route::get('platform/hq-central/fleet/deployments/{deployment}', [\App\Http\Controllers\Admin\HQFleetController::class, 'deploymentShow'])->name('platform.hq_central.fleet.deployments.show');
        Route::get('platform/hq-central/fleet/channels', [\App\Http\Controllers\Admin\HQFleetController::class, 'channels'])->name('platform.hq_central.fleet.channels');
        Route::get('platform/hq-central/fleet/groups', [\App\Http\Controllers\Admin\HQFleetController::class, 'groups'])->name('platform.hq_central.fleet.groups');
        Route::get('platform/hq-central/fleet/maintenance', [\App\Http\Controllers\Admin\HQFleetController::class, 'maintenance'])->name('platform.hq_central.fleet.maintenance');        
        Route::get('platform/hq-central/alerts', [\App\Http\Controllers\Admin\HQAlertController::class, 'index'])->name('platform.hq_central.alerts.index');
        Route::get('platform/hq-central/alerts/{alert}', [\App\Http\Controllers\Admin\HQAlertController::class, 'show'])->name('platform.hq_central.alerts.show');
        Route::post('platform/hq-central/alerts/{alert}/acknowledge', [\App\Http\Controllers\Admin\HQAlertController::class, 'acknowledge'])->name('platform.hq_central.alerts.acknowledge');
        Route::post('platform/hq-central/alerts/{alert}/resolve', [\App\Http\Controllers\Admin\HQAlertController::class, 'resolve'])->name('platform.hq_central.alerts.resolve');
        
        Route::get('platform/hq-central/commands/{command}', [\App\Http\Controllers\Admin\HQRemoteCommandController::class, 'show'])->name('platform.hq_central.commands.show');
        
        // HQ Billing & Subscriptions
        Route::get('platform/hq-central/billing/plans', [\App\Http\Controllers\Admin\HQBillingController::class, 'plans'])->name('platform.hq_central.billing.plans');
        Route::get('platform/hq-central/billing/subscriptions', [\App\Http\Controllers\Admin\HQBillingController::class, 'subscriptions'])->name('platform.hq_central.billing.subscriptions');
        Route::get('platform/hq-central/billing/invoices', [\App\Http\Controllers\Admin\HQBillingController::class, 'invoices'])->name('platform.hq_central.billing.invoices');
        Route::get('platform/hq-central/billing/payments', [\App\Http\Controllers\Admin\HQBillingController::class, 'payments'])->name('platform.hq_central.billing.payments');

        Route::get('platform/hq-central/tenants', [\App\Http\Controllers\Admin\HQTenantController::class, 'index'])->name('platform.hq_central.tenants.index');
        Route::post('platform/hq-central/tenants', [\App\Http\Controllers\Admin\HQTenantController::class, 'store'])->name('platform.hq_central.tenants.store');
        Route::put('platform/hq-central/tenants/{tenant}', [\App\Http\Controllers\Admin\HQTenantController::class, 'update'])->name('platform.hq_central.tenants.update');
        
        Route::get('platform/hq-central/licenses', [\App\Http\Controllers\Admin\HQLicenseController::class, 'index'])->name('platform.hq_central.licenses.index');
        Route::post('platform/hq-central/licenses', [\App\Http\Controllers\Admin\HQLicenseController::class, 'store'])->name('platform.hq_central.licenses.store');
        Route::get('platform/hq-central/licenses/{license}', [\App\Http\Controllers\Admin\HQLicenseController::class, 'show'])->name('platform.hq_central.licenses.show');
        Route::post('platform/hq-central/licenses/{license}/activate', [\App\Http\Controllers\Admin\HQLicenseController::class, 'activate'])->name('platform.hq_central.licenses.activate');
        Route::post('platform/hq-central/licenses/{license}/suspend', [\App\Http\Controllers\Admin\HQLicenseController::class, 'suspend'])->name('platform.hq_central.licenses.suspend');
        Route::post('platform/hq-central/licenses/{license}/features', [\App\Http\Controllers\Admin\HQLicenseController::class, 'toggleFeature'])->name('platform.hq_central.licenses.toggleFeature');
        
        Route::get('platform/hq-integration', [\App\Http\Controllers\Admin\HQIntegrationController::class, 'index'])->name('platform.hq_integration.index');
        Route::get('platform/api', [\App\Http\Controllers\Admin\HQApiController::class, 'index'])->name('platform.api.index');
        Route::post('platform/api/regenerate', [\App\Http\Controllers\Admin\HQApiController::class, 'regenerate'])->name('platform.api.regenerate');
        Route::post('platform/api/revoke', [\App\Http\Controllers\Admin\HQApiController::class, 'revoke'])->name('platform.api.revoke');
        Route::get('platform/sync', [\App\Http\Controllers\Admin\HQSyncController::class, 'index'])->name('platform.sync.index');
        Route::get('platform/communication', [\App\Http\Controllers\Admin\HQCommunicationController::class, 'index'])->name('platform.communication.index');
        Route::post('platform/communication/ping', [\App\Http\Controllers\Admin\HQCommunicationController::class, 'ping'])->name('platform.communication.ping');
        Route::post('platform/communication/health', [\App\Http\Controllers\Admin\HQCommunicationController::class, 'health'])->name('platform.communication.health');
        Route::post('platform/communication/register', [\App\Http\Controllers\Admin\HQCommunicationController::class, 'register'])->name('platform.communication.register');
        Route::post('platform/communication/sync', [\App\Http\Controllers\Admin\HQCommunicationController::class, 'sync'])->name('platform.communication.sync');
        
        Route::get('platform/commands', [\App\Http\Controllers\Admin\HQCommandController::class, 'index'])->name('platform.commands.index');
        Route::post('platform/commands/{command}/approve', [\App\Http\Controllers\Admin\HQCommandController::class, 'approve'])->name('platform.commands.approve');
        Route::post('platform/commands/{command}/reject', [\App\Http\Controllers\Admin\HQCommandController::class, 'reject'])->name('platform.commands.reject');
        Route::post('platform/commands/{command}/execute', [\App\Http\Controllers\Admin\HQCommandController::class, 'execute'])->name('platform.commands.execute');
        
        Route::get('platform/telemetry', [\App\Http\Controllers\Admin\HQTelemetryController::class, 'index'])->name('platform.telemetry.index');
        Route::post('platform/telemetry/send', [\App\Http\Controllers\Admin\HQTelemetryController::class, 'send'])->name('platform.telemetry.send');
        
        Route::get('platform/scheduler', [\App\Http\Controllers\Admin\HQSchedulerController::class, 'index'])->name('platform.scheduler.index');
    });

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
        Route::resource('notifications', NotificationController::class)->only(['index', 'store']);
    });

    // System Jobs
    Route::middleware(['permission:system.jobs.manage'])->group(function () {
        Route::get('system/jobs', [SystemJobController::class, 'dashboard'])->name('system.jobs.dashboard');
        Route::get('system/jobs/failed', [SystemJobController::class, 'failed'])->name('system.jobs.failed');
        Route::get('system/jobs/history', [SystemJobController::class, 'history'])->name('system.jobs.history');
        Route::get('system/automation-logs', [SystemJobController::class, 'automation'])->name('system.jobs.automation');
    });

    // Education - Students & Announcements
    Route::middleware(['permission:students.view'])->group(function () {
        Route::get('students/analytics', [StudentController::class, 'analytics'])->name('students.analytics');
        Route::post('students/{student}/transfer', [StudentController::class, 'transfer'])->name('students.transfer');
        Route::post('students/enrollment', [StudentEnrollmentController::class, 'store'])->name('students.enrollment.store');
        Route::resource('students', StudentController::class)->except(['show']);
        Route::resource('announcements', AnnouncementController::class)->only(['index', 'store']);
        
        // Exams
        Route::get('exams/analytics', [ExamResultController::class, 'analytics'])->name('exams.analytics');
        Route::get('exams/{exam}/results', [ExamResultController::class, 'index'])->name('exams.results.index');
        Route::post('exams/{exam}/results', [ExamResultController::class, 'store'])->name('exams.results.store');
        Route::resource('exams', ExamController::class)->only(['index', 'store']);
    });

    // Education - Attendance
    Route::middleware(['permission:attendance.view'])->group(function () {
        Route::get('attendances/analytics', [AttendanceSessionController::class, 'analytics'])->name('attendances.analytics');
        Route::get('attendances/sessions/{session}/take', [AttendanceSessionController::class, 'take'])->name('attendances.sessions.take');
        Route::post('attendances/sessions/{session}/take', [AttendanceSessionController::class, 'storeBulk'])->name('attendances.sessions.store-bulk');
        Route::resource('attendances/sessions', AttendanceSessionController::class)->only(['index', 'store'])->names('attendances.sessions');
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
    });

    // Education - Teachers
    Route::middleware(['permission:teachers.view'])->group(function () {
        Route::get('teachers/{teacher}/performance', [TeacherPerformanceController::class, 'show'])->name('teachers.performance');
        Route::post('teachers/performance', [TeacherPerformanceController::class, 'store'])->name('teachers.performance.store');
        Route::get('teachers/{teacher}/analytics', [TeacherController::class, 'analytics'])->name('teachers.analytics');
        Route::resource('teachers', TeacherController::class)->only(['index', 'store', 'edit', 'update']);
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
        Route::resource('classrooms', ClassroomController::class)->except(['show']);
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

        Route::resource('attendance', EmployeeAttendanceController::class)->only(['index', 'store']);

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
    });
});
