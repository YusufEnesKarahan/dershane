<?php

use App\Models\Branch;
use App\Models\User;
use App\Models\Role;
use App\Models\Teacher;
use App\Models\Student;
use App\Models\StudentGuardian;
use App\Models\Classroom;
use App\Models\Course;
use App\Models\AcademicTerm;
use App\Models\SystemIdentity;
use App\Domain\Institution\Models\InstitutionSetting;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== Seeding RCAT Demo Data ===\n";

// Ensure Roles Exist
$superAdminRole = Role::firstOrCreate(['name' => 'Super Admin']);
$branchAdminRole = Role::firstOrCreate(['name' => 'Branch Admin']);
$teacherRole = Role::firstOrCreate(['name' => 'Teacher']);
$studentRole = Role::firstOrCreate(['name' => 'Student']);
$parentRole = Role::firstOrCreate(['name' => 'Parent']);

// Seed System Identity and Term
SystemIdentity::firstOrCreate(
    ['company_name' => 'Demo Dershane SaaS A.Ş.'],
    ['uuid' => Str::uuid(), 'brand_name' => 'Dershane SaaS']
);

$term = AcademicTerm::firstOrCreate(
    ['is_active' => true],
    ['name' => '2025-2026 Akademik Yılı', 'start_date' => now()->subMonths(3), 'end_date' => now()->addMonths(6)]
);

// Create 3 Institutions / Branches
$branches = [];
$branchConfigs = [
    ['name' => 'Kadıköy Merkez Şubesi', 'code' => 'KDK-01', 'slug' => 'kadikoy-merkez'],
    ['name' => 'Beşiktaş Kampüs Şubesi', 'code' => 'BSK-01', 'slug' => 'besiktas-kampus'],
    ['name' => 'Bakırköy Şubesi', 'code' => 'BKR-01', 'slug' => 'bakirkoy-subesi'],
];

foreach ($branchConfigs as $index => $config) {
    $branch = Branch::firstOrCreate(
        ['slug' => $config['slug']],
        ['name' => $config['name'], 'code' => $config['code'], 'status' => 'active']
    );
    $branches[] = $branch;

    // Institution Settings per branch
    InstitutionSetting::firstOrCreate(
        ['branch_id' => $branch->id],
        [
            'institution_name' => $config['name'],
            'phone' => '0212000000' . ($index + 1),
            'email' => 'info@' . $config['slug'] . '.com',
            'address' => $config['name'] . ' Adres Sokak No:' . ($index + 1),
            'city' => 'İstanbul',
            'district' => 'Kadıköy',
            'primary_color' => '#4f46e5',
            'secondary_color' => '#0f172a',
            'timezone' => 'Europe/Istanbul',
            'language' => 'tr',
        ]
    );

    // Branch Admin
    $admin = User::firstOrCreate(
        ['email' => 'admin' . ($index + 1) . '@test.com'],
        [
            'name' => $config['name'] . ' Yöneticisi',
            'password' => Hash::make('password'),
            'branch_id' => $branch->id,
            'status' => 'ACTIVE',
        ]
    );
    $admin->roles()->syncWithoutDetaching([$branchAdminRole->id]);
}

// Super Admin
$superAdmin = User::firstOrCreate(
    ['email' => 'superadmin@test.com'],
    [
        'name' => 'Super Admin Manager',
        'password' => Hash::make('password'),
        'branch_id' => null,
        'status' => 'ACTIVE',
    ]
);
$superAdmin->roles()->syncWithoutDetaching([$superAdminRole->id]);

// Create Courses & Classrooms for Branch 1
$branch = $branches[0];

$mathCourse = Course::firstOrCreate(['code' => 'MAT-101'], ['name' => 'Matematik', 'slug' => 'matematik', 'branch_id' => $branch->id]);
$physCourse = Course::firstOrCreate(['code' => 'FIZ-101'], ['name' => 'Fizik', 'slug' => 'fizik', 'branch_id' => $branch->id]);

$classroom = Classroom::firstOrCreate(['code' => '12-A'], ['name' => '12-A Sayısal', 'branch_id' => $branch->id, 'capacity' => 30]);

// Create 20 Teachers
echo "Creating 20 Teachers...\n";
for ($i = 1; $i <= 20; $i++) {
    $teacherUser = User::firstOrCreate(
        ['email' => "teacher{$i}@test.com"],
        [
            'name' => "Öğretmen Test {$i}",
            'password' => Hash::make('password'),
            'branch_id' => $branch->id,
            'status' => 'ACTIVE',
        ]
    );
    $teacherUser->roles()->syncWithoutDetaching([$teacherRole->id]);

    Teacher::firstOrCreate(
        ['user_id' => $teacherUser->id],
        [
            'branch_id' => $branch->id,
            'title' => 'Matematik Öğretmeni',
            'specialties' => 'Matematik, Fizik',
            'status' => 'ACTIVE',
        ]
    );
}

// Create 200 Students & Parents
echo "Creating 200 Students & Parents...\n";
for ($i = 1; $i <= 200; $i++) {
    $studentUser = User::firstOrCreate(
        ['email' => "student{$i}@test.com"],
        [
            'name' => "Öğrenci Test {$i}",
            'password' => Hash::make('password'),
            'branch_id' => $branch->id,
            'status' => 'ACTIVE',
        ]
    );
    $studentUser->roles()->syncWithoutDetaching([$studentRole->id]);

    $student = Student::firstOrCreate(
        ['user_id' => $studentUser->id],
        [
            'branch_id' => $branch->id,
            'classroom_id' => $classroom->id,
            'first_name' => "Öğrenci",
            'last_name' => "Test {$i}",
            'student_number' => "STD-" . sprintf("%04d", $i),
            'status' => 'ACTIVE',
        ]
    );

    $parentUser = User::firstOrCreate(
        ['email' => "parent{$i}@test.com"],
        [
            'name' => "Veli Test {$i}",
            'password' => Hash::make('password'),
            'branch_id' => $branch->id,
            'status' => 'ACTIVE',
        ]
    );
    $parentUser->roles()->syncWithoutDetaching([$parentRole->id]);

    StudentGuardian::firstOrCreate(
        ['user_id' => $parentUser->id, 'student_id' => $student->id],
        [
            'guardian_name' => "Veli Test {$i}",
            'relation' => 'FATHER',
            'phone' => "0555000" . sprintf("%04d", $i),
            'is_primary' => true,
        ]
    );
}

echo "=== RCAT Demo Data Seeding Completed Successfully! ===\n";
