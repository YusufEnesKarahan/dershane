<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$emails = [
    'superadmin@test.com',
    'admin1@test.com',
    'teacher1@test.com',
    'student1@test.com',
    'parent1@test.com',
];

echo "=== ROLES IN DATABASE ===\n";
foreach (App\Models\Role::all() as $r) {
    echo "Role ID: {$r->id} | Name: '{$r->name}'\n";
}

echo "\n=== CORE TEST USERS ===\n";
foreach ($emails as $email) {
    $u = App\Models\User::where('email', $email)->first();
    if (!$u) {
        echo "User {$email} NOT FOUND\n";
        continue;
    }
    $roleNames = $u->roles->pluck('name')->toArray();
    echo "ID: {$u->id} | Email: {$u->email} | Branch ID: " . ($u->branch_id ?? 'null') . " | Roles: [" . implode(', ', $roleNames) . "]\n";
    
    // Check permissions
    $perms = app(\App\Domain\Auth\Services\EffectivePermissionService::class)->effectivePermissions($u);
    echo "  Perms Count: " . count($perms) . "\n";
    if (in_array('dashboard.view', $perms)) echo "  -> HAS 'dashboard.view'\n";
}
