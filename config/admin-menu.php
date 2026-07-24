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
                    'title' => 'Finance & Invoices',
                    'route' => 'admin.invoices.index',
                    'permission' => 'students.view',
                ],
                [
                    'title' => 'Finance Dashboard',
                    'route' => 'admin.invoices.dashboard',
                    'permission' => 'students.view',
                ],
                [
                    'title' => 'Bildirim Merkezi',
                    'route' => 'admin.notifications.dashboard',
                    'permission' => 'notifications.view',
                ],
                [
                    'title' => 'Announcements',
                    'route' => 'admin.announcements.index',
                    'permission' => 'students.view',
                ],
                [
                    'title' => 'Bildirim Analitiği',
                    'route' => 'admin.notifications.analytics',
                    'permission' => 'notifications.view',
                ],
                [
                    'title' => 'Parent Portal',
                    'route' => 'parent.dashboard',
                    'permission' => 'students.view',
                ],
                [
                    'title' => 'Teacher Portal',
                    'route' => 'teacher.dashboard',
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
            'title' => 'Reporting & BI',
            'icon' => 'chart-bar',
            'permission' => 'dashboard.view',
            'sort' => 4,
            'children' => [
                [
                    'title' => 'Executive Dashboard',
                    'route' => 'admin.reporting.dashboard',
                    'permission' => 'dashboard.view',
                ],
                [
                    'title' => 'BI Analytics',
                    'route' => 'admin.reporting.analytics',
                    'permission' => 'dashboard.view',
                ],
                [
                    'title' => 'Reports & Schedules',
                    'route' => 'admin.reporting.reports',
                    'permission' => 'dashboard.view',
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
                    'title' => 'CRM Dashboard',
                    'route' => 'admin.crm.dashboard',
                    'permission' => 'crm.view',
                ],
                [
                    'title' => 'Leads',
                    'route' => 'admin.leads.index',
                    'permission' => 'crm.view',
                ],
                [
                    'title' => 'Pipeline',
                    'route' => 'admin.crm.pipeline',
                    'permission' => 'crm.view',
                ],
                [
                    'title' => 'Takipler (Followups)',
                    'route' => 'admin.crm.followups',
                    'permission' => 'crm.view',
                ],
                [
                    'title' => 'CRM Analiz',
                    'route' => 'admin.crm.analytics',
                    'permission' => 'crm.view',
                ],
                [
                    'title' => 'Contact Messages',
                    'route' => 'admin.contacts.index',
                    'permission' => 'contacts.view',
                ],
            ],
        ],
        [
            'title' => 'Kayıt Yönetimi',
            'icon' => 'user-plus',
            'permission' => 'admission.view',
            'sort' => 5,
            'children' => [
                [
                    'title' => 'Ön Kayıt Dashboard',
                    'route' => 'admin.admission.dashboard',
                    'permission' => 'admission.view',
                ],
                [
                    'title' => 'Ön Kayıt Başvuruları',
                    'route' => 'admin.admission.index',
                    'permission' => 'admission.view',
                ],
                [
                    'title' => 'Kayıt Workflow',
                    'route' => 'admin.admission.workflow',
                    'permission' => 'admission.view',
                ],
                [
                    'title' => 'Evrak Yönetimi',
                    'route' => 'admin.admission.documents',
                    'permission' => 'admission.view',
                ],
                [
                    'title' => 'Kayıt Sözleşmeleri',
                    'route' => 'admin.contracts.index',
                    'permission' => 'admission.view',
                ],
            ],
        ],
        [
            'title' => 'İnsan Kaynakları',
            'icon' => 'users',
            'permission' => 'hr.view',
            'sort' => 6,
            'children' => [
                [
                    'title' => 'İK Paneli',
                    'route' => 'admin.hr.dashboard',
                    'permission' => 'hr.view',
                ],
                [
                    'title' => 'Personel Listesi',
                    'route' => 'admin.employees.index',
                    'permission' => 'hr.view',
                ],
                [
                    'title' => 'Departmanlar',
                    'route' => 'admin.departments.index',
                    'permission' => 'hr.view',
                ],
                [
                    'title' => 'Maaş & Bordro',
                    'route' => 'admin.payroll.index',
                    'permission' => 'hr.view',
                ],
                [
                    'title' => 'İzin İstekleri',
                    'route' => 'admin.leaves.index',
                    'permission' => 'hr.view',
                ],
                [
                    'title' => 'Giriş / Çıkış',
                    'route' => 'admin.attendance.index',
                    'permission' => 'hr.view',
                ],
                [
                    'title' => 'Performans',
                    'route' => 'admin.performance.index',
                    'permission' => 'hr.view',
                ],
                [
                    'title' => 'Masraflar',
                    'route' => 'admin.expenses.index',
                    'permission' => 'hr.view',
                ],
                [
                    'title' => 'Avanslar',
                    'route' => 'admin.advances.index',
                    'permission' => 'hr.view',
                ],
                [
                    'title' => 'İK Analitiği',
                    'route' => 'admin.hr.analytics',
                    'permission' => 'hr.view',
                ],
            ],
        ],
        [
            'title' => 'Envanter ve Demirbaş',
            'icon' => 'archive',
            'permission' => 'assets.view',
            'sort' => 6,
            'children' => [
                [
                    'title' => 'Paneli',
                    'route' => 'admin.inventory.dashboard',
                    'permission' => 'assets.view',
                ],
                [
                    'title' => 'Demirbaş Listesi',
                    'route' => 'admin.assets.index',
                    'permission' => 'assets.view',
                ],
                [
                    'title' => 'Kategoriler & Konum',
                    'route' => 'admin.categories.index',
                    'permission' => 'assets.view',
                ],
                [
                    'title' => 'Zimmet Listesi',
                    'route' => 'admin.assignments.index',
                    'permission' => 'assets.view',
                ],
                [
                    'title' => 'Stok & Malzeme',
                    'route' => 'admin.inventory.index',
                    'permission' => 'assets.view',
                ],
                [
                    'title' => 'Tedarikçiler',
                    'route' => 'admin.suppliers.index',
                    'permission' => 'assets.view',
                ],
                [
                    'title' => 'Satın Alma',
                    'route' => 'admin.purchase.index',
                    'permission' => 'assets.view',
                ],
                [
                    'title' => 'Bakım & Onarım',
                    'route' => 'admin.maintenance.index',
                    'permission' => 'assets.view',
                ],
                [
                    'title' => 'Transferler',
                    'route' => 'admin.transfers.index',
                    'permission' => 'assets.view',
                ],
                [
                    'title' => 'Envanter Analitiği',
                    'route' => 'admin.inventory.analytics',
                    'permission' => 'assets.view',
                ],
            ],
        ],
        [
            'title' => 'Dijital Arşiv & Dokümanlar',
            'icon' => 'folder-open',
            'permission' => 'documents.view',
            'sort' => 7,
            'children' => [
                [
                    'title' => 'Arşiv Paneli',
                    'route' => 'admin.documents.dashboard',
                    'permission' => 'documents.view',
                ],
                [
                    'title' => 'Tüm Belgeler',
                    'route' => 'admin.documents.index',
                    'permission' => 'documents.view',
                ],
                [
                    'title' => 'Yeni Doküman Yükle',
                    'route' => 'admin.documents.create',
                    'permission' => 'documents.manage',
                ],
                [
                    'title' => 'Kategori Yönetimi',
                    'route' => 'admin.document-categories.index',
                    'permission' => 'documents.manage',
                ],
                [
                    'title' => 'Arşiv Analitiği',
                    'route' => 'admin.documents.analytics',
                    'permission' => 'documents.view',
                ],
            ],
        ],
        [
            'title' => 'System',
            'icon' => 'cog',
            'permission' => 'settings.view',
            'sort' => 6,
            'children' => [
                [ 'title' => 'Queue Dashboard', 'route' => 'admin.system.jobs.dashboard', 'permission' => 'system.jobs.manage' ],
                [ 'title' => 'Failed Jobs', 'route' => 'admin.system.jobs.failed', 'permission' => 'system.jobs.manage' ],
                [ 'title' => 'Automation Logs', 'route' => 'admin.system.jobs.automation', 'permission' => 'system.jobs.manage' ],
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
