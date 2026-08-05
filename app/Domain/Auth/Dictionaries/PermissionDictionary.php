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

    public const TEACHERS_VIEW = 'teachers.view';
    public const TEACHERS_CREATE = 'teachers.create';
    public const TEACHERS_UPDATE = 'teachers.update';
    public const TEACHERS_DELETE = 'teachers.delete';

    public const TEACHER_VIEW = 'teacher.view';
    public const TEACHER_CREATE = 'teacher.create';
    public const TEACHER_UPDATE = 'teacher.update';
    public const TEACHER_DELETE = 'teacher.delete';

    public const TEACHER_DOCUMENTS_MANAGE = 'teacher.documents.manage';
    public const TEACHER_PERFORMANCE_MANAGE = 'teacher.performance.manage';
    public const TEACHER_SCHEDULE_MANAGE = 'teacher.schedule.manage';
    public const TEACHER_ANALYTICS_MANAGE = 'teacher.analytics.manage';

    public const COURSES_VIEW = 'courses.view';
    public const COURSES_CREATE = 'courses.create';
    public const COURSES_UPDATE = 'courses.update';
    public const COURSES_DELETE = 'courses.delete';

    public const ATTENDANCE_VIEW = 'attendance.view';
    public const ATTENDANCE_CREATE = 'attendance.create';
    public const ATTENDANCE_UPDATE = 'attendance.update';
    public const ATTENDANCE_DELETE = 'attendance.delete';
    public const ATTENDANCE_REPORT = 'attendance.report';

    public const HOMEWORKS_VIEW = 'homeworks.view';
    public const HOMEWORKS_MANAGE = 'homeworks.manage';

    public const CLASSROOMS_VIEW = 'classrooms.view';
    public const CLASSROOMS_MANAGE = 'classrooms.manage';
    public const CLASSROOM_VIEW = 'classroom.view';

    public const STUDENTS_VIEW = 'students.view';
    public const STUDENTS_CREATE = 'students.create';
    public const STUDENTS_UPDATE = 'students.update';
    public const STUDENTS_DELETE = 'students.delete';

    public const STUDENT_VIEW = 'student.view';
    public const STUDENT_CREATE = 'student.create';
    public const STUDENT_UPDATE = 'student.update';
    public const STUDENT_DELETE = 'student.delete';

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

    // Parent & Student Portal
    public const PARENT_VIEW_CHILD = 'parent.view_child';
    public const PARENT_VIEW_ATTENDANCE = 'parent.view_attendance';
    public const STUDENT_VIEW_PROFILE = 'student.view_profile';
    public const STUDENT_VIEW_SCHEDULE = 'student.view_schedule';

    // Exams
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

    public const SCHEDULE_VIEW = 'schedule.view';
    public const SCHEDULE_CREATE = 'schedule.create';
    public const SCHEDULE_UPDATE = 'schedule.update';
    public const SCHEDULE_DELETE = 'schedule.delete';

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

    public const NOTIFICATION_VIEW = 'notification.view';
    public const NOTIFICATION_CREATE = 'notification.create';
    public const NOTIFICATION_SEND = 'notification.send';
    public const NOTIFICATION_MANAGE = 'notification.manage';

    public const ONBOARDING_VIEW = 'onboarding.view';
    public const ONBOARDING_MANAGE = 'onboarding.manage';

    public const INSTITUTION_SETTINGS_VIEW = 'institution.settings.view';
    public const INSTITUTION_SETTINGS_UPDATE = 'institution.settings.update';

    public const USER_VIEW = 'user.view';
    public const USER_CREATE = 'user.create';
    public const USER_UPDATE = 'user.update';
    public const USER_DELETE = 'user.delete';
    public const ROLE_MANAGE = 'role.manage';

    public static function all(): array
    {
        $oClass = new \ReflectionClass(__CLASS__);
        return array_values($oClass->getConstants());
    }
}
