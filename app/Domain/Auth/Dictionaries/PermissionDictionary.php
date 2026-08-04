<?php
namespace App\Domain\Auth\Dictionaries;

class PermissionDictionary
{
    public const DASHBOARD_VIEW = 'dashboard.view';

    public const USERS_VIEW = 'users.view';
    public const USERS_CREATE = 'users.create';
    public const USERS_UPDATE = 'users.update';
    public const USERS_DELETE = 'users.delete';
    public const USERS_RESTORE = 'users.restore';
    public const USERS_EXPORT = 'users.export';

    public const ROLES_VIEW = 'roles.view';
    public const ROLES_CREATE = 'roles.create';
    public const ROLES_UPDATE = 'roles.update';
    public const ROLES_DELETE = 'roles.delete';
    public const ROLES_ASSIGN = 'roles.assign';

    public const PERMISSIONS_VIEW = 'permissions.view';
    public const PERMISSIONS_ASSIGN = 'permissions.assign';

    public const PAGES_VIEW = 'pages.view';
    public const PAGES_CREATE = 'pages.create';
    public const PAGES_UPDATE = 'pages.update';
    public const PAGES_DELETE = 'pages.delete';
    public const PAGES_PUBLISH = 'pages.publish';
    public const PAGES_ARCHIVE = 'pages.archive';
    public const PAGES_RESTORE = 'pages.restore';
    public const PAGES_PREVIEW = 'pages.preview';
    public const PAGES_REVISION = 'pages.revision';

    public const BLOGS_VIEW = 'blogs.view';
    public const BLOGS_CREATE = 'blogs.create';
    public const BLOGS_UPDATE = 'blogs.update';
    public const BLOGS_DELETE = 'blogs.delete';

    public const TEACHERS_VIEW = 'teachers.view';
    public const TEACHERS_CREATE = 'teachers.create';
    public const TEACHERS_UPDATE = 'teachers.update';
    public const TEACHERS_DELETE = 'teachers.delete';

    public const TEACHER_DOCUMENTS_MANAGE = 'teacher.documents.manage';
    public const TEACHER_PERFORMANCE_MANAGE = 'teacher.performance.manage';
    public const TEACHER_SCHEDULE_MANAGE = 'teacher.schedule.manage';
    public const TEACHER_SALARY_MANAGE = 'teacher.salary.manage';
    public const TEACHER_CONTRACTS_MANAGE = 'teacher.contracts.manage';
    public const TEACHER_ANALYTICS_MANAGE = 'teacher.analytics.manage';

    public const COURSES_VIEW = 'courses.view';
    public const COURSES_CREATE = 'courses.create';
    public const COURSES_UPDATE = 'courses.update';
    public const COURSES_DELETE = 'courses.delete';

    public const GALLERY_VIEW = 'gallery.view';
    public const GALLERY_CREATE = 'gallery.create';
    public const GALLERY_DELETE = 'gallery.delete';

    public const MEDIA_VIEW = 'media.view';
    public const MEDIA_CREATE = 'media.create';
    public const MEDIA_UPDATE = 'media.update';
    public const MEDIA_DELETE = 'media.delete';
    public const MEDIA_MOVE = 'media.move';
    public const MEDIA_DOWNLOAD = 'media.download';

    public const SETTINGS_VIEW = 'settings.view';
    public const SETTINGS_UPDATE = 'settings.update';

    public const CRM_VIEW = 'crm.view';
    public const CRM_MANAGE = 'crm.manage';

    public const ATTENDANCE_VIEW = 'attendance.view';
    public const ATTENDANCE_CREATE = 'attendance.create';
    public const ATTENDANCE_UPDATE = 'attendance.update';
    public const ATTENDANCE_DELETE = 'attendance.delete';
    public const ATTENDANCE_REPORT = 'attendance.report';

    public const HOMEWORKS_VIEW = 'homeworks.view';
    public const HOMEWORKS_MANAGE = 'homeworks.manage';

    public const CLASSROOMS_VIEW = 'classrooms.view';
    public const CLASSROOMS_MANAGE = 'classrooms.manage';

    public const STUDENTS_VIEW = 'students.view';
    public const STUDENTS_CREATE = 'students.create';
    public const STUDENTS_UPDATE = 'students.update';
    public const STUDENTS_DELETE = 'students.delete';

    public const LEADS_VIEW = 'leads.view';
    public const LEADS_CREATE = 'leads.create';
    public const LEADS_UPDATE = 'leads.update';
    public const LEADS_DELETE = 'leads.delete';

    public const CONTACTS_VIEW = 'contacts.view';
    public const CONTACTS_CREATE = 'contacts.create';
    public const CONTACTS_UPDATE = 'contacts.update';
    public const CONTACTS_DELETE = 'contacts.delete';

    public const REGISTRATIONS_VIEW = 'registrations.view';
    public const REGISTRATIONS_CREATE = 'registrations.create';
    public const REGISTRATIONS_UPDATE = 'registrations.update';
    public const REGISTRATIONS_DELETE = 'registrations.delete';

    public const ANNOUNCEMENTS_VIEW = 'announcements.view';
    public const ANNOUNCEMENTS_CREATE = 'announcements.create';
    public const ANNOUNCEMENTS_UPDATE = 'announcements.update';
    public const ANNOUNCEMENTS_DELETE = 'announcements.delete';

    public const NOTIFICATIONS_VIEW = 'notifications.view';
    public const NOTIFICATIONS_MANAGE = 'notifications.manage';
    public const SYSTEM_JOBS_MANAGE = 'system.jobs.manage';

    public const BRANCHES_VIEW = 'branches.view';
    public const BRANCHES_MANAGE = 'branches.manage';

    public const ADMISSION_VIEW = 'admission.view';
    public const ADMISSION_CREATE = 'admission.create';
    public const ADMISSION_UPDATE = 'admission.update';
    public const ADMISSION_APPROVE = 'admission.approve';
    public const ENROLLMENT_MANAGE = 'enrollment.manage';

    public const HR_VIEW = 'hr.view';
    public const HR_CREATE = 'hr.create';
    public const HR_UPDATE = 'hr.update';
    public const HR_APPROVE = 'hr.approve';
    public const PAYROLL_MANAGE = 'payroll.manage';

    public const ASSETS_VIEW = 'assets.view';
    public const ASSETS_MANAGE = 'assets.manage';

    public const INVENTORY_VIEW = 'inventory.view';
    public const INVENTORY_MANAGE = 'inventory.manage';

    public const PURCHASE_VIEW = 'purchase.view';
    public const PURCHASE_MANAGE = 'purchase.manage';

    public const DOCUMENTS_VIEW = 'documents.view';
    public const DOCUMENTS_MANAGE = 'documents.manage';

    public const FINANCE_VIEW = 'finance.view';
    public const FINANCE_MANAGE = 'finance.manage';
    public const FINANCE_CREATE = 'finance.create';
    public const FINANCE_UPDATE = 'finance.update';
    public const FINANCE_DELETE = 'finance.delete';
    public const FINANCE_COLLECT = 'finance.collect';
    public const FINANCE_REFUND = 'finance.refund';
    public const FINANCE_DISCOUNT = 'finance.discount';

    public const NOTIFICATIONS_READ = 'notifications.read';
    // Parent & Student Portal (Sprint 8.6)
    public const PARENT_VIEW_CHILD = 'parent.view_child';
    public const PARENT_VIEW_ATTENDANCE = 'parent.view_attendance';
    public const STUDENT_VIEW_PROFILE = 'student.view_profile';
    public const STUDENT_VIEW_SCHEDULE = 'student.view_schedule';

    // Exams (Sprint 9.4)
    public const EXAM_VIEW = 'exam.view';
    public const EXAM_CREATE = 'exam.create';
    public const EXAM_UPDATE = 'exam.update';
    public const EXAM_DELETE = 'exam.delete';
    public const EXAM_PUBLISH = 'exam.publish';
    public const EXAM_RESULT_CREATE = 'exam.result.create';
    public const EXAM_RESULT_VIEW = 'exam.result.view';
    public const EXAM_REPORT = 'exam.report';

    public const SCHEDULES_VIEW = 'schedules.view';
    public const SCHEDULES_CREATE = 'schedules.create';
    public const SCHEDULES_UPDATE = 'schedules.update';
    public const SCHEDULES_DELETE = 'schedules.delete';
    public const SCHEDULES_MANAGE = 'schedules.manage';

    public const HOMEWORK_VIEW = 'homework.view';
    public const HOMEWORK_CREATE = 'homework.create';
    public const HOMEWORK_UPDATE = 'homework.update';
    public const HOMEWORK_DELETE = 'homework.delete';
    public const HOMEWORK_PUBLISH = 'homework.publish';
    public const HOMEWORK_GRADE = 'homework.grade';
    public const HOMEWORK_SUBMIT = 'homework.submit';
    public const HOMEWORK_REPORT = 'homework.report';

    public const HQ_BACKUP_MANAGE = 'hq.manageBackup';
    public const HQ_AUDIT_VIEW = 'hq.viewAuditLogs';
    public const EXAM_RESULTS_MANAGE = 'exam_results.manage';

    public const GUIDANCE_VIEW = 'guidance.view';
    public const GUIDANCE_CREATE = 'guidance.create';
    public const GUIDANCE_UPDATE = 'guidance.update';
    public const GUIDANCE_DELETE = 'guidance.delete';
    public const GUIDANCE_MEETING = 'guidance.meeting';
    public const GUIDANCE_GOAL = 'guidance.goal';
    public const GUIDANCE_NOTE = 'guidance.note';

    public static function all(): array
    {
        $oClass = new \ReflectionClass(__CLASS__);
        return array_values($oClass->getConstants());
    }
}
