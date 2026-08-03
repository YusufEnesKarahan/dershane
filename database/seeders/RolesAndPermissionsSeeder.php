<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\{Role, Permission, User};
use App\Domain\Auth\Services\PermissionManager;
use App\Domain\Auth\Services\RoleManager;
use App\Domain\Auth\Services\PermissionCache;

use App\Domain\Auth\Dictionaries\PermissionDictionary;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(PermissionManager $permManager, RoleManager $roleManager, PermissionCache $cache): void
    {
        $permissions = PermissionDictionary::all();

        $permissionIds = [];
        foreach ($permissions as $permName) {
            $permissionIds[$permName] = $permManager->createPermission($permName)->id;
        }

        $roles = [
            'Super Admin' => [], // Gets all implicitly via Gate::before
            'Admin' => [
                'dashboard.view', 'users.*', 'roles.*', 'teachers.*', 'courses.*', 
                'classrooms.*', 'branches.*', 'crm.*', 'leads.*', 'contacts.*',
                'hr.*', 'payroll.*', 'assets.*', 'inventory.*', 'purchase.*',
                'admission.*', 'enrollment.*', 'documents.*', 'settings.*', 'system.*',
                'finance.*'
            ],
            'Teacher' => [
                'dashboard.view',
                'students.view', 'courses.view', 'classrooms.view',
                'attendance.view', 'attendance.create', 'attendance.update', 'attendance.report',
                'homeworks.view', 'homeworks.manage'
            ],
            'Secretary' => [
                'dashboard.view',
                'students.*', 'registrations.*', 'leads.*', 'contacts.*',
                'admission.*', 'enrollment.*'
            ],
            'Accountant' => [
                'dashboard.view',
                'registrations.*', 'payroll.*', 'hr.view',
                'assets.*', 'inventory.*', 'purchase.*', 'finance.*'
            ],
            'Parent' => [
                'dashboard.view',
                'students.view', 'notifications.view', 'attendance.view',
                'homeworks.view', 'registrations.view'
            ],
            'Student' => [
                'dashboard.view',
                'students.view', 'notifications.view', 'attendance.view',
                'homeworks.view'
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
                    if (isset($permissionIds[$permSpec])) {
                        $idsToAssign[] = $permissionIds[$permSpec];
                    }
                }
            }
            
            if (count($idsToAssign) > 0) {
                $roleManager->assignPermissionToRole($role, $idsToAssign);
            }
        }

        $adminRole = Role::where('name', 'Super Admin')->first();
        $adminUser = User::firstOrCreate(
            ['email' => 'admin@dershane.com'],
            ['name' => 'Admin User', 'password' => bcrypt('password')]
        );
        $adminUser->roles()->syncWithoutDetaching([$adminRole->id]);
    }
}
