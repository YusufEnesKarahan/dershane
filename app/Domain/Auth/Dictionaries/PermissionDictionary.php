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
    public const ATTENDANCE_MANAGE = 'attendance.manage';

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

    public const BRANCHES_VIEW = 'branches.view';
    public const BRANCHES_MANAGE = 'branches.manage';

    public static function all(): array
    {
        $oClass = new \ReflectionClass(__CLASS__);
        return array_values($oClass->getConstants());
    }
}
