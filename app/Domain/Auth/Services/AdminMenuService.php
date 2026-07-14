<?php

namespace App\Domain\Auth\Services;

use Illuminate\Support\Facades\Auth;

class AdminMenuService
{
    public function getSidebarMenu(): array
    {
        $menu = [];

        if ($this->shouldShow('dashboard.view')) {
            $menu[] = [
                'title' => 'Dashboard',
                'route' => 'admin.dashboard',
                'icon' => 'home',
            ];
        }

        if ($this->shouldShow('users.view') || $this->shouldShow('roles.view') || $this->shouldShow('permissions.view')) {
            $menu[] = [
                'title' => 'Access Management',
                'icon' => 'users',
                'children' => array_filter([
                    $this->shouldShow('users.view') ? ['title' => 'Users', 'route' => 'admin.users.index'] : null,
                    $this->shouldShow('roles.view') ? ['title' => 'Roles', 'route' => 'admin.roles.index'] : null,
                    $this->shouldShow('permissions.view') ? ['title' => 'Permissions', 'route' => 'admin.permissions.index'] : null,
                ])
            ];
        }

        if ($this->shouldShow('pages.view') || $this->shouldShow('blogs.view')) {
            $menu[] = [
                'title' => 'CMS',
                'icon' => 'document-text',
                'children' => array_filter([
                    $this->shouldShow('pages.view') ? ['title' => 'Pages', 'route' => 'admin.pages.index'] : null,
                    $this->shouldShow('blogs.view') ? ['title' => 'Blogs', 'route' => 'admin.blogs.index'] : null,
                ])
            ];
        }

        if ($this->shouldShow('teachers.view') || $this->shouldShow('courses.view') || $this->shouldShow('classrooms.view')) {
            $menu[] = [
                'title' => 'Education',
                'icon' => 'academic-cap',
                'children' => array_filter([
                    $this->shouldShow('students.view') ? ['title' => 'Students', 'route' => 'admin.students.index'] : null,
                    $this->shouldShow('teachers.view') ? ['title' => 'Teachers', 'route' => 'admin.teachers.index'] : null,
                    $this->shouldShow('courses.view') ? ['title' => 'Courses', 'route' => 'admin.courses.index'] : null,
                    $this->shouldShow('classrooms.view') ? ['title' => 'Classrooms', 'route' => 'admin.classrooms.index'] : null,
                ])
            ];
        }

        if ($this->shouldShow('crm.view')) {
            $menu[] = [
                'title' => 'CRM',
                'icon' => 'phone',
                'children' => array_filter([
                    $this->shouldShow('leads.view') ? ['title' => 'Leads', 'route' => 'admin.leads.index'] : null,
                    $this->shouldShow('contacts.view') ? ['title' => 'Contact Messages', 'route' => 'admin.contacts.index'] : null,
                ])
            ];
        }

        if ($this->shouldShow('settings.view') || $this->shouldShow('branches.view')) {
            $menu[] = [
                'title' => 'System',
                'icon' => 'cog',
                'children' => array_filter([
                    $this->shouldShow('branches.view') ? ['title' => 'Branches', 'route' => 'admin.branches.index'] : null,
                    $this->shouldShow('settings.view') ? ['title' => 'Settings', 'route' => 'admin.settings.index'] : null,
                    $this->shouldShow('logs.view') ? ['title' => 'System Logs', 'route' => 'admin.logs.index'] : null,
                ])
            ];
        }

        return $menu;
    }

    protected function shouldShow(string $permission, ?string $edition = null, ?string $feature = null): bool
    {
        $user = Auth::user();
        if (!$user) {
            return false;
        }

        // 1. Permission check (using effective permissions computation)
        $effectivePermissions = app(\App\Domain\Auth\Services\EffectivePermissionService::class)->effectivePermissions($user);
        
        // Administrator always bypassed
        if ($user->hasRole('Administrator')) {
            return true;
        }

        // Wildcard check
        $hasPerm = false;
        foreach ($effectivePermissions as $userPerm) {
            if ($userPerm === $permission) {
                $hasPerm = true;
                break;
            }
            if (str_ends_with($userPerm, '.*')) {
                $prefix = substr($userPerm, 0, -1);
                if (str_starts_with($permission, $prefix)) {
                    $hasPerm = true;
                    break;
                }
            }
        }

        if (!$hasPerm) {
            return false;
        }

        // 2. Edition check (basic, professional, ultimate)
        if ($edition && function_exists('edition')) {
            $currentEdition = edition()->current();
            if ($edition === 'professional' && $currentEdition === 'basic') {
                return false;
            }
            if ($edition === 'ultimate' && $currentEdition !== 'ultimate') {
                return false;
            }
        }

        // 3. Feature check
        if ($feature && function_exists('feature')) {
            if (!feature()->active($feature)) {
                return false;
            }
        }

        return true;
    }
}
