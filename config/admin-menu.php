<?php

return [
    'menu' => [
        [
            'title' => 'Dashboard',
            'icon' => 'home',
            'route' => 'admin.dashboard',
            'permission' => 'dashboard.view',
            'sort' => 1,
        ],
        [
            'title' => 'Access Management',
            'icon' => 'users',
            'permission' => 'users.view', // checked by visibility resolver
            'sort' => 2,
            'children' => [
                [
                    'title' => 'Users',
                    'route' => 'admin.users.index',
                    'permission' => 'users.view',
                ],
                [
                    'title' => 'Roles',
                    'route' => 'admin.roles.index',
                    'permission' => 'roles.view',
                ],
                [
                    'title' => 'Permissions',
                    'route' => 'admin.permissions.index',
                    'permission' => 'permissions.view',
                ],
            ],
        ],
        [
            'title' => 'CMS',
            'icon' => 'document-text',
            'permission' => 'pages.view',
            'sort' => 3,
            'children' => [
                [
                    'title' => 'Pages',
                    'route' => 'admin.pages.index',
                    'permission' => 'pages.view',
                ],
                [
                    'title' => 'Media Library',
                    'route' => 'admin.media.index',
                    'permission' => 'media.view',
                ],
                [
                    'title' => 'Blogs',
                    'route' => 'admin.blogs.index',
                    'permission' => 'blogs.view',
                ],
                [
                    'title' => 'Blog Categories',
                    'route' => 'admin.categories.index',
                    'permission' => 'categories.view',
                ],
                [
                    'title' => 'Blog Tags',
                    'route' => 'admin.tags.index',
                    'permission' => 'tags.view',
                ],
                [
                    'title' => 'Comments Moderation',
                    'route' => 'admin.comments.index',
                    'permission' => 'comments.view',
                ],
            ],
        ],
        [
            'title' => 'Education',
            'icon' => 'academic-cap',
            'permission' => 'teachers.view',
            'sort' => 4,
            'children' => [
                [
                    'title' => 'Students',
                    'route' => 'admin.students.index',
                    'permission' => 'students.view',
                ],
                [
                    'title' => 'Student Analytics',
                    'route' => 'admin.students.analytics',
                    'permission' => 'students.view',
                ],
                [
                    'title' => 'Attendance Sessions',
                    'route' => 'admin.attendances.sessions.index',
                    'permission' => 'students.view',
                ],
                [
                    'title' => 'Attendance Analytics',
                    'route' => 'admin.attendances.analytics',
                    'permission' => 'students.view',
                ],
                [
                    'title' => 'Exams',
                    'route' => 'admin.exams.index',
                    'permission' => 'students.view',
                ],
                [
                    'title' => 'Exam Analytics',
                    'route' => 'admin.exams.analytics',
                    'permission' => 'students.view',
                ],
                [
                    'title' => 'Assignments',
                    'route' => 'admin.assignments.index',
                    'permission' => 'students.view',
                ],
                [
                    'title' => 'Homework Analytics',
                    'route' => 'admin.assignments.analytics',
                    'permission' => 'students.view',
                ],
                [
                    'title' => 'Teachers',
                    'route' => 'admin.teachers.index',
                    'permission' => 'teachers.view',
                ],
                [
                    'title' => 'Teacher Schedules',
                    'route' => 'admin.teachers.schedules.index',
                    'permission' => 'teachers.view',
                ],
                [
                    'title' => 'Teacher Performance',
                    'route' => 'admin.teachers.performance.index',
                    'permission' => 'teachers.view',
                ],
                [
                    'title' => 'Teacher Salary',
                    'route' => 'admin.teachers.salary.index',
                    'permission' => 'teachers.view',
                ],
                [
                    'title' => 'Teacher Contracts',
                    'route' => 'admin.teachers.contracts.index',
                    'permission' => 'teachers.view',
                ],
                [
                    'title' => 'Courses',
                    'route' => 'admin.courses.index',
                    'permission' => 'courses.view',
                ],
                [
                    'title' => 'Course Levels',
                    'route' => 'admin.courses.levels.index',
                    'permission' => 'courses.view',
                ],
                [
                    'title' => 'Course Analytics',
                    'route' => 'admin.courses.analytics',
                    'permission' => 'courses.view',
                ],
                [
                    'title' => 'Classrooms',
                    'route' => 'admin.classrooms.index',
                    'permission' => 'classrooms.view',
                ],
                [
                    'title' => 'Weekly Schedule',
                    'route' => 'admin.classrooms.schedules.index',
                    'permission' => 'classrooms.view',
                ],
                [
                    'title' => 'Academic Calendar',
                    'route' => 'admin.classrooms.academic-calendar.index',
                    'permission' => 'classrooms.view',
                ],
                [
                    'title' => 'Holidays',
                    'route' => 'admin.classrooms.holidays.index',
                    'permission' => 'classrooms.view',
                ],
            ],
        ],
        [
            'title' => 'CRM',
            'icon' => 'phone',
            'permission' => 'crm.view',
            'sort' => 5,
            'children' => [
                [
                    'title' => 'Leads',
                    'route' => 'admin.leads.index',
                    'permission' => 'leads.view',
                ],
                [
                    'title' => 'Contact Messages',
                    'route' => 'admin.contacts.index',
                    'permission' => 'contacts.view',
                ],
            ],
        ],
        [
            'title' => 'System',
            'icon' => 'cog',
            'permission' => 'settings.view',
            'sort' => 6,
            'children' => [
                [
                    'title' => 'Branches',
                    'route' => 'admin.branches.index',
                    'permission' => 'branches.view',
                ],
                [
                    'title' => 'Settings',
                    'route' => 'admin.settings.index',
                    'permission' => 'settings.view',
                ],
            ],
        ],
    ],
];
