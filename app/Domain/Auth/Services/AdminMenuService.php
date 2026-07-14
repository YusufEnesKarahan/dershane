<?php

namespace App\Domain\Auth\Services;

use Illuminate\Support\Facades\Auth;

class AdminMenuService
{
    public function getSidebarMenu(): array
    {
        $user = Auth::user();
        if (!$user) {
            return [];
        }

        $menu = [];

        if ($user->hasPermission('dashboard.view')) {
            $menu[] = [
                'title' => 'Dashboard',
                'route' => 'admin.dashboard',
                'icon' => 'home',
            ];
        }

        if ($user->hasPermission('users.view') || $user->hasPermission('roles.view') || $user->hasPermission('permissions.view')) {
            $menu[] = [
                'title' => 'Access Management',
                'icon' => 'users',
                'children' => array_filter([
                    $user->hasPermission('users.view') ? ['title' => 'Users', 'route' => 'admin.users.index'] : null,
                    $user->hasPermission('roles.view') ? ['title' => 'Roles', 'route' => 'admin.roles.index'] : null,
                    $user->hasPermission('permissions.view') ? ['title' => 'Permissions', 'route' => 'admin.permissions.index'] : null,
                ])
            ];
        }

        if ($user->hasPermission('pages.view') || $user->hasPermission('blogs.view') || $user->hasPermission('announcements.view')) {
            $menu[] = [
                'title' => 'CMS',
                'icon' => 'document-text',
                'children' => array_filter([
                    $user->hasPermission('pages.view') ? ['title' => 'Pages', 'route' => 'admin.pages.index'] : null,
                    $user->hasPermission('blogs.view') ? ['title' => 'Blogs', 'route' => 'admin.blogs.index'] : null,
                    $user->hasPermission('announcements.view') ? ['title' => 'Announcements', 'route' => 'admin.announcements.index'] : null,
                ])
            ];
        }

        if ($user->hasPermission('students.view') || $user->hasPermission('teachers.view') || $user->hasPermission('courses.view')) {
            $menu[] = [
                'title' => 'Education',
                'icon' => 'academic-cap',
                'children' => array_filter([
                    $user->hasPermission('students.view') ? ['title' => 'Students', 'route' => 'admin.students.index'] : null,
                    $user->hasPermission('teachers.view') ? ['title' => 'Teachers', 'route' => 'admin.teachers.index'] : null,
                    $user->hasPermission('courses.view') ? ['title' => 'Courses', 'route' => 'admin.courses.index'] : null,
                    $user->hasPermission('classrooms.view') ? ['title' => 'Classrooms', 'route' => 'admin.classrooms.index'] : null,
                ])
            ];
        }

        if ($user->hasPermission('leads.view') || $user->hasPermission('contacts.view')) {
            $menu[] = [
                'title' => 'CRM',
                'icon' => 'phone',
                'children' => array_filter([
                    $user->hasPermission('leads.view') ? ['title' => 'Leads', 'route' => 'admin.leads.index'] : null,
                    $user->hasPermission('contacts.view') ? ['title' => 'Contact Messages', 'route' => 'admin.contacts.index'] : null,
                ])
            ];
        }

        if ($user->hasPermission('settings.view') || $user->hasPermission('branches.view') || $user->hasPermission('logs.view')) {
            $menu[] = [
                'title' => 'System',
                'icon' => 'cog',
                'children' => array_filter([
                    $user->hasPermission('branches.view') ? ['title' => 'Branches', 'route' => 'admin.branches.index'] : null,
                    $user->hasPermission('settings.view') ? ['title' => 'Settings', 'route' => 'admin.settings.index'] : null,
                    $user->hasPermission('logs.view') ? ['title' => 'System Logs', 'route' => 'admin.logs.index'] : null,
                ])
            ];
        }

        return $menu;
    }
}
