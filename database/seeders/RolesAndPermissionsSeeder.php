<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\{Role, Permission, User};
use App\Domain\Auth\Services\PermissionManager;
use App\Domain\Auth\Services\RoleManager;
use App\Domain\Auth\Services\PermissionCache;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(PermissionManager $permManager, RoleManager $roleManager, PermissionCache $cache): void
    {
        $permissions = [
            'dashboard.view',
            'users.view', 'users.create', 'users.update', 'users.delete',
            'roles.view', 'roles.create', 'roles.update', 'roles.delete',
            'permissions.view', 'permissions.assign',
            'pages.view', 'pages.create', 'pages.update', 'pages.delete',
            'blogs.view', 'blogs.create', 'blogs.update', 'blogs.delete',
            'gallery.view', 'gallery.create', 'gallery.update', 'gallery.delete',
            'announcements.view', 'announcements.create', 'announcements.update', 'announcements.delete',
            'students.view', 'students.create', 'students.update', 'students.delete',
            'teachers.view', 'teachers.create', 'teachers.update', 'teachers.delete',
            'courses.view', 'courses.create', 'courses.update', 'courses.delete',
            'classrooms.view', 'classrooms.create', 'classrooms.update', 'classrooms.delete',
            'registrations.view', 'registrations.create', 'registrations.update', 'registrations.delete',
            'leads.view', 'leads.create', 'leads.update', 'leads.delete',
            'contacts.view', 'contacts.create', 'contacts.update', 'contacts.delete',
            'settings.view', 'settings.update',
            'branches.view', 'branches.create', 'branches.update', 'branches.delete',
            'media.view', 'media.create', 'media.delete',
            'logs.view',
        ];

        $permissionIds = [];
        foreach ($permissions as $permName) {
            $permissionIds[$permName] = $permManager->createPermission($permName)->id;
        }

        $roles = [
            'Administrator' => [], // Gets all implicitly via Gate::before
            'Branch Manager' => [
                'dashboard.view',
                'students.*', 'teachers.*', 'courses.*', 'classrooms.*', 'registrations.*',
                'leads.*', 'contacts.*',
                'branches.view',
            ],
            'Teacher' => [
                'dashboard.view',
                'students.view', 'courses.view', 'classrooms.view'
            ],
            'Reception' => [
                'dashboard.view',
                'students.view', 'students.create', 'registrations.view', 'registrations.create',
                'leads.view', 'leads.create', 'contacts.view'
            ],
            'Marketing' => [
                'dashboard.view',
                'pages.view', 'pages.update',
                'blogs.*', 'gallery.*', 'announcements.*',
                'leads.view', 'media.*'
            ],
            'Editor' => [
                'dashboard.view',
                'pages.*', 'blogs.*', 'gallery.*', 'announcements.*', 'media.*'
            ],
            'Viewer' => [
                'dashboard.view',
                'users.view', 'pages.view', 'blogs.view', 'students.view', 'courses.view'
            ]
        ];

        foreach ($roles as $roleName => $perms) {
            $role = Role::firstOrCreate(['name' => $roleName]);
            
            $idsToAssign = [];
            foreach ($perms as $permSpec) {
                if (str_ends_with($permSpec, '.*')) {
                    $prefix = substr($permSpec, 0, -2);
                    foreach ($permissions as $p) {
                        if (str_starts_with($p, $prefix)) {
                            $idsToAssign[] = $permissionIds[$p];
                        }
                    }
                } else {
                    $idsToAssign[] = $permissionIds[$permSpec];
                }
            }
            
            if (count($idsToAssign) > 0) {
                $roleManager->assignPermissionToRole($role, $idsToAssign);
            }
        }

        $adminRole = Role::where('name', 'Administrator')->first();
        $adminUser = User::firstOrCreate(
            ['email' => 'admin@dershane.com'],
            ['name' => 'Admin User', 'password' => bcrypt('password')]
        );
        $adminUser->roles()->syncWithoutDetaching([$adminRole->id]);
    }
}
