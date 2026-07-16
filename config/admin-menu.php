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
                    'title' => 'Teachers',
                    'route' => 'admin.teachers.index',
                    'permission' => 'teachers.view',
                ],
                [
                    'title' => 'Courses',
                    'route' => 'admin.courses.index',
                    'permission' => 'courses.view',
                ],
                [
                    'title' => 'Classrooms',
                    'route' => 'admin.classrooms.index',
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
