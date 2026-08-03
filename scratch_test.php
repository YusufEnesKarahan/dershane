<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$branch = App\Models\Branch::factory()->create(['slug' => 'test-branch-' . uniqid(), 'name' => 'Test Branch']);
$user = App\Models\User::factory()->create(['branch_id' => $branch->id, 'status' => App\Enums\UserStatus::ACTIVE]);
$role = App\Models\Role::firstOrCreate(['name' => 'Student']);
$user->roles()->attach($role->id);

$count = App\Models\User::whereHas('roles', function($q) { $q->where('name', 'Student'); })
    ->where('status', App\Enums\UserStatus::ACTIVE->value)->count();
echo "Count is: " . $count . "\n";

$notification = new \App\Notifications\GeneralNotification('Title', 'Content', 'announcement');
$user->notify($notification);

echo "Notification count is: " . $user->notifications()->count() . "\n";
