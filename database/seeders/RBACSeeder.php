<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Role;
use App\Models\Permission;

class RBACSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roles = [
            'Super Admin' => 'Tüm sistemi yönetir.',
            'tenant_admin' => 'Kurum yöneticisi.',
            'teacher' => 'Öğretmen.',
            'staff' => 'Kurum personeli.'
        ];

        foreach ($roles as $name => $description) {
            Role::firstOrCreate(['name' => $name], ['description' => $description]);
        }

        $permissions = [
            'students.view',
            'students.create',
            'students.update',
            'students.delete',
            
            'teachers.view',
            'teachers.create',
            'teachers.update',
            'teachers.delete',

            'classes.view',
            'classes.create',
            'classes.update',
            'classes.delete',
            
            'reports.view',
            'attendance.take',
        ];

        foreach ($permissions as $perm) {
            Permission::firstOrCreate(['name' => $perm]);
        }

        // Attach permissions to roles
        $tenantAdmin = Role::where('name', 'tenant_admin')->first();
        if ($tenantAdmin) {
            $tenantAdminPermissions = Permission::whereIn('name', [
                'students.view', 'students.create', 'students.update', 'students.delete',
                'teachers.view', 'teachers.create', 'teachers.update', 'teachers.delete',
                'classes.view', 'classes.create', 'classes.update', 'classes.delete',
                'reports.view'
            ])->get();
            $tenantAdmin->permissions()->syncWithoutDetaching($tenantAdminPermissions->pluck('id'));
        }

        $teacher = Role::where('name', 'teacher')->first();
        if ($teacher) {
            $teacherPermissions = Permission::whereIn('name', [
                'students.view', 'classes.view', 'attendance.take'
            ])->get();
            $teacher->permissions()->syncWithoutDetaching($teacherPermissions->pluck('id'));
        }

        $staff = Role::where('name', 'staff')->first();
        if ($staff) {
            $staffPermissions = Permission::whereIn('name', [
                'students.view', 'students.create', 'students.update', 'classes.view'
            ])->get();
            $staff->permissions()->syncWithoutDetaching($staffPermissions->pluck('id'));
        }
    }
}
